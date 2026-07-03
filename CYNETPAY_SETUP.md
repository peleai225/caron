# Configuration CynetPay

## Variables d'environnement

Ajoutez ces variables dans votre fichier `.env` :

```env
# CynetPay Configuration
CYNETPAY_API_KEY=votre_api_key_cynetpay
CYNETPAY_SITE_ID=votre_site_id
CYNETPAY_BASE_URL=https://api.cynetpay.com/v1
CYNETPAY_TEST_MODE=true
```

## Configuration SMS

```env
# SMS Configuration (exemple avec API générique)
SMS_API_KEY=votre_api_key_sms
SMS_API_URL=https://api.sms-provider.com/v1
SMS_SENDER_ID=CARON
```

## Configuration WhatsApp

```env
# WhatsApp Business API Configuration
WHATSAPP_API_KEY=votre_whatsapp_api_key
WHATSAPP_API_URL=https://graph.facebook.com/v18.0
WHATSAPP_PHONE_NUMBER_ID=votre_phone_number_id
```

## Notes importantes

1. **CynetPay** : Obtenez vos identifiants depuis votre compte marchand CynetPay
2. **SMS** : Configurez selon votre fournisseur SMS (Orange, MTN, etc.)
3. **WhatsApp** : Utilisez l'API WhatsApp Business de Meta (Facebook)

