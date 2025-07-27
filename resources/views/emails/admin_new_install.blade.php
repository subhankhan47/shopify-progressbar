@component('mail::message')
    # 🚀 New App Installation

    A new store has installed the app:

    **🛍 Store Name:** {{ $shop['storeName'] ?? 'N/A' }}
    **📧 Store Email:** {{ $shop['email'] ?? 'N/A' }}

    **💳 Plan:** {{ $shop['plan']->name ?? '' }}
    **📅 Interval:** {{ $shop['plan']->interval ?? '' }}

    **🕐 Installed At:** {{ $installed_at }}

    Thanks,
    **The {{ config('app.name') }} Team**
@endcomponent
