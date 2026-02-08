use App\Models\Tenant;
use App\Models\Run;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AiAnalysisService
{
    /**
     * Check if tenant is within their monthly AI budget.
     */
    public function isWithinBudget(Tenant $tenant): bool
    {
        $monthlyCost = Run::where('tenant_id', $tenant->id)
            ->where('type', 'analysis')
            ->where('started_at', '>=', now()->startOfMonth())
            ->get()
            ->sum(fn($run) => (float) ($run->meta['stats']['total_cost'] ?? 0));

        return $monthlyCost < (float) $tenant->ai_monthly_budget_usd;
    }
    /**
     * Analyze a signal using OpenAI.
     *
     * @param string $domain
     * @param string $title
     * @param string $content
     * @return array
     */
    public function analyze(string $domain, string $title, string $content): array
    {
        $apiKey = config('ai.openai.api_key');
        if (!$apiKey) {
            throw new \Exception("OpenAI API key not configured.");
        }

        $model = config('ai.openai.model');
        $prompt = $this->buildPrompt($domain, $title, $content);

        $response = Http::withToken($apiKey)
            ->timeout(30)
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => $model,
                'messages' => [
                    ['role' => 'system', 'content' => 'You are an intelligence analysis engine. You output strictly valid JSON.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'response_format' => ['type' => 'json_object'],
            ]);

        if ($response->failed()) {
            throw new \Exception("AI Analysis API failure: " . $response->body());
        }

        $data = $response->json();
        $content = json_decode($data['choices'][0]['message']['content'], true);

        return [
            'summary' => $content['summary'] ?? 'Failed to generate summary.',
            'implications' => $content['implications'] ?? 'Failed to generate implications.',
            'action_required' => (int) ($content['action_required'] ?? 0),
            'usage' => [
                'prompt_tokens' => $data['usage']['prompt_tokens'],
                'completion_tokens' => $data['usage']['completion_tokens'],
                'total_tokens' => $data['usage']['total_tokens'],
                'model' => $model,
                'cost' => $this->calculateCost($model, $data['usage']['prompt_tokens'], $data['usage']['completion_tokens']),
            ],
        ];
    }

    /**
     * Build the prompt based on domain.
     */
    protected function buildPrompt(string $domain, string $title, string $content): string
    {
        $templates = config('ai.prompts');
        $template = $templates[$domain] ?? $templates['default'];
        
        // Clean content to avoid prompt injection or overflow (trim to 4000 chars roughly)
        $cleanContent = Str::limit($content, 4000);

        return strtr($template, [
            '{domain}' => $domain,
            '{title}' => $title,
            '{content}' => $cleanContent,
            '{format_instruction}' => config('ai.format_instruction'),
        ]);
    }

    /**
     * Calculate cost based on usage and pricing config.
     */
    protected function calculateCost(string $model, int $promptTokens, int $completionTokens): float
    {
        $pricing = config("ai.pricing.{$model}", config('ai.pricing.gpt-4o-mini'));
        
        return ($promptTokens / 1000 * $pricing['input']) + 
               ($completionTokens / 1000 * $pricing['output']);
    }
}
