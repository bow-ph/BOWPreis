# BOW Preishoheit Plugin

Dynamic price adjustments for Shopware products based on Preishoheit API integration.

## Features
- Product selection for dynamic pricing
- Flexible pricing settings
- Price import history
- Comprehensive error logging
- Automated processing via cron jobs
- Secure API key management
- Bilingual interface (German/English)

## Requirements
- Shopware 6.6.x
- Valid Preishoheit API key

## Installation
1. Upload the plugin to your Shopware installation
2. Install and activate the plugin through the plugin manager
3. Configure your Preishoheit API key in the plugin settings

## Configuration

### API Key Setup
1. Navigate to Settings > System > BOW Preishoheit
2. Open the "API Configuration" tab
3. Enter your Preishoheit API key in the secure input field
4. Click "Verify API Key" to test the connection
5. Save your configuration after successful verification

### Error Handling
The plugin includes comprehensive error handling with detailed logging:

- API Communication Errors:
  - Invalid API key
  - Connection timeout
  - Server errors
  - Logged to `var/log/bow_preishoheit_api.log`

- Configuration Errors:
  - Missing API key
  - Invalid settings
  - Logged to `var/log/bow_preishoheit_config.log`

- System Errors:
  - Unexpected errors
  - Critical failures
  - Logged to `var/log/bow_preishoheit_system.log`

### Error Messages

#### German
- API-Schlüssel ist erforderlich
- API-Schlüssel konnte nicht verifiziert werden
- Fehler beim Speichern der Konfiguration

#### English
- API key is required
- Could not verify API key
- Error saving configuration

## Testing

### Running Tests
```bash
# Unit Tests
./vendor/bin/phpunit tests/Unit

# Integration Tests
./vendor/bin/phpunit tests/Integration
```

### Test Coverage
- Unit tests for API verification and error handling
- Integration tests for API communication
- End-to-end tests for admin interface

## Development

### Directory Structure
```
BOWPreishoheit/
├── src/
│   ├── Controller/
│   ├── Exception/
│   ├── Service/
│   └── Resources/
│       └── app/
│           └── administration/
└── tests/
    ├── Unit/
    └── Integration/
```

### Best Practices
- Follow Shopware coding standards
- Use dependency injection
- Implement comprehensive error handling
- Include bilingual support
- Write unit and integration tests

## Support
For support inquiries, please contact:
- Email: support@bow-agentur.de
- Website: https://www.bow-agentur.de
