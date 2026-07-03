<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject }}</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 30px; text-align: center; border-radius: 10px 10px 0 0;">
        <h1 style="color: white; margin: 0;">{{ config('app.name', 'Caron') }}</h1>
    </div>
    
    <div style="background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px;">
        <h2 style="color: #333; margin-top: 0;">{{ $subject }}</h2>
        
        <div style="background: white; padding: 20px; border-radius: 5px; margin: 20px 0;">
            <p style="margin: 0; white-space: pre-line;">{{ $message }}</p>
        </div>
        
        <p style="color: #666; font-size: 12px; margin-top: 30px;">
            Cet email a été envoyé depuis {{ config('app.name', 'Caron') }} - Gestion Immobilière
        </p>
    </div>
</body>
</html>

