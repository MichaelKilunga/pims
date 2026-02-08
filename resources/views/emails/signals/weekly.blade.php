<x-mail::message>
# Weekly Strategic Intelligence Summary
## Week of {{ now()->startOfWeek()->format('M d') }} - {{ now()->endOfWeek()->format('M d, Y') }}

Summary of key trends and critical alerts from the past 7 days.

@foreach($groupedSignals as $domainName => $signals)
@php
    $highPriorityCount = $signals->where('action_required', '>=', 1)->count();
@endphp

## 📊 {{ $domainName }} ({{ count($signals) }} signals, {{ $highPriorityCount }} priority)

@foreach($signals->where('action_required', '>=', 1) as $signal)
<x-mail::panel>
### [CRITICAL] {{ $signal->title }}
**Implications:** {{ $signal->implications }}
</x-mail::panel>
@endforeach

---
@endforeach

<x-mail::button :url="config('app.url')">
View Full Dashboard
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
