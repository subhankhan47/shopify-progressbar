@component('mail::message')
    # ⚠️ App Uninstalled
    The following store has uninstalled the app:

    **🛍 Store Name:** {{ $shop->name ?? 'N/A' }}
    **📧 Store Email:** {{ $shop->email ?? 'N/A' }}
    **🕐 Uninstalled At:** {{ $uninstalled_at }}

    Thanks,<br>
    The {{ config('app.name') ?? 'SF Reward Bar' }} Team
@endcomponent
