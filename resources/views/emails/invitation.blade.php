<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invitation — {{ config('app.name') }}</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Inter', sans-serif; background: #f8fafc; margin: 0; padding: 40px 20px; color: #1e293b; }
        .container { max-width: 520px; margin: 0 auto; background: #ffffff; border-radius: 12px; border: 1px solid #e2e8f0; overflow: hidden; }
        .header { background: #4f46e5; padding: 32px 40px; text-align: center; }
        .header h1 { color: #ffffff; font-size: 20px; font-weight: 600; margin: 0; }
        .body { padding: 32px 40px; }
        .body p { font-size: 14px; line-height: 1.7; color: #475569; margin: 0 0 16px; }
        .body strong { color: #1e293b; }
        .btn { display: inline-block; margin: 24px 0; padding: 14px 32px; background: #4f46e5; color: #ffffff !important; text-decoration: none; border-radius: 8px; font-size: 14px; font-weight: 600; }
        .note { font-size: 12px; color: #94a3b8; margin-top: 24px; padding-top: 24px; border-top: 1px solid #e2e8f0; }
        .footer { padding: 16px 40px; background: #f8fafc; text-align: center; font-size: 11px; color: #94a3b8; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ config('app.name') }}</h1>
        </div>
        <div class="body">
            <p>Bonjour <strong>{{ $user->name }}</strong>,</p>
            <p>
                Vous avez été invité(e) à rejoindre <strong>{{ config('app.name') }}</strong>
                @if($user->agency) en tant que membre de l'agence <strong>{{ $user->agency->name }}</strong>@endif.
            </p>
            <p>Cliquez sur le bouton ci-dessous pour activer votre compte et choisir votre mot de passe :</p>

            <div style="text-align: center;">
                <a href="{{ $invitationUrl }}" class="btn">Activer mon compte</a>
            </div>

            <p class="note">
                Ce lien est valable <strong>48 heures</strong>. Passé ce délai, demandez à votre administrateur de renvoyer l'invitation.<br><br>
                Si vous n'attendiez pas cette invitation, ignorez cet email.
            </p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} {{ config('app.name') }} — Gestion immobilière
        </div>
    </div>
</body>
</html>
