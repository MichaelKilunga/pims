<x-mail::message>
# Daily Intelligence Digest
@if($tenant)
## Client: {{ $tenant->name }}
@endif
## {{ now()->format('l, F j, Y') }}

Here is your summarized intelligence for the last 24 hours.

@foreach($groupedSignals as $domainName => $signals)
## 📂 {{ $domainName }}

@foreach($signals as $signal)
<x-mail::panel>
### [{{ $signal->title }}]({{ $signal->url }})
**Priority:** 
@if($signal->action_required == 2)
🔴 **ACT**
@elseif($signal->action_required == 1)
🟠 **WATCH**
@else
⚪️ Routine
@endif

**Summary:**  
{{ $signal->summary }}

**Strategic Implications:**  
*{{ $signal->implications }}*

[View Source Article]({{ $signal->url }})
</x-mail::panel>
@endforeach

---
@endforeach

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
