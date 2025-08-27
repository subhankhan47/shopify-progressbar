@component('mail::message')
    <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" role="presentation" style="width: 100%; max-width: 600px; border-collapse: collapse; margin: 0 auto;">
                    <tr>
                        <td style="padding: 20px 30px; text-align: center; background-color: #f7f7f7;">
                            <h1 style="margin: 0; font-size: 24px; font-family: Arial, sans-serif; color: #333;">
                                👋 Sorry to See You Go
                            </h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 30px; font-family: Arial, sans-serif; font-size: 16px; color: #555;">
                            <p style="margin-bottom: 20px;">
                                Hi {{ $shop->name ?? 'there' }},
                            </p>
                            <p style="margin-bottom: 20px;">
                                We're sorry to see you uninstall the <strong>{{ config('app.name') ?? 'SF Reward Bar' }}</strong> app.
                            </p>
                            <p style="margin-bottom: 20px;">
                                If there’s anything we could’ve done better or if you’d like to share feedback, please let us know by replying to this email.
                            </p>
                            <p style="margin-bottom: 10px;">
                                📞 <strong>WhatsApp Support:</strong> <a href="https://wa.me/447366278042" target="_blank" style="color: #007bff;">+447366278042</a>
                            </p>
                            <p style="margin-bottom: 30px;">
                                📧 <strong>Email Support:</strong> <a href="mailto:support@sfaddons.com" style="color: #007bff;">support@sfaddons.com</a>
                            </p>
                            <p style="margin-bottom: 30px;">
                                We’d love to help you again in the future.
                            </p>
                            <p style="margin-top: 40px;">
                                The {{ config('app.name') ?? 'SF Reward Bar' }} Team
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
@endcomponent
