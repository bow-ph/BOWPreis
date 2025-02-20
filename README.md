# BOW Preishoheit Plugin

## Overview
The BOWPreishoheit plugin enables dynamic price adjustments for Shopware products based on imported data from the Preishoheit API. It provides a comprehensive solution for automated price management with real-time updates and detailed tracking.

## Key Features
- Product selection for dynamic pricing
- Flexible pricing settings
- Price import history
- Comprehensive error logging
- Automated processing via cron jobs
- Secure API key management
- Bilingual interface (German/English)

## Requirements
- Shopware 6.6.x
- PHP 8.1 or higher
- Valid Preishoheit API key
- Active internet connection for API communication
- Cron service for automated updates

## Installation
1. Upload the plugin to your Shopware installation
2. Install and activate the plugin through the plugin manager
3. Configure your Preishoheit API key in the plugin settings

## Configuration

### API Configuration
1. Navigate to Settings > System > BOW Preishoheit
2. Open the "API Configuration" tab
3. Enter your Preishoheit API key in the secure input field
4. Click "Verify API Key" to test the connection
5. Save your configuration after successful verification

### Product Selection
1. Access the "Product Selection" tab
2. Use the grid view to select products for price management
3. Configure individual surcharge/discount percentages
4. Enable/disable price updates per product
5. Use bulk actions for efficient management

### Price Update Settings
1. Configure global price adjustment settings
2. Set up automated update intervals (1min - 24hrs)
3. Use manual update button for immediate price updates
4. Monitor update status and results

### API Endpoints

#### Price Update Endpoints
```
POST /api/_action/bow-preishoheit/update-prices
- Triggers manual price update
- Returns: { "success": true/false, "message": "string" }

POST /api/_action/bow-preishoheit/verify-api-key
- Verifies API key configuration
- Body: { "apiKey": "string" }
- Returns: { "success": true/false, "message": "string" }
```

### Component Usage

#### Product Grid
```javascript
// Template usage
<bow-preishoheit-product-grid></bow-preishoheit-product-grid>

// Features
- Product selection with pagination
- Inline editing for surcharge/discount
- Bulk selection capabilities
- Sorting and filtering
```

#### Price History
```javascript
// Template usage
<bow-preishoheit-price-history></bow-preishoheit-price-history>

// Features
- Tabular view of price changes
- Date/time tracking
- EAN/GTIN tracking
- Price comparison
```

#### Settings Component
```javascript
// Template usage
<bow-preishoheit-settings></bow-preishoheit-settings>

// Features
- API key management
- Manual update trigger
- Configuration options
```

### Error Handling
The plugin includes comprehensive error handling with detailed logging:

#### API Communication Errors
- Invalid API key
- Connection timeout
- Server errors
- Logged to `var/log/bow_preishoheit_api.log`

#### Configuration Errors
- Missing API key
- Invalid settings
- Logged to `var/log/bow_preishoheit_config.log`

#### System Errors
- Unexpected errors
- Critical failures
- Logged to `var/log/bow_preishoheit_system.log`

### Implementation Notes

#### Error Handling Service
```php
// Use ErrorLogger service for consistent error handling
$this->errorLogger->logApiError($error, $context);
$this->errorLogger->logConfigurationError($error);
$this->errorLogger->logSystemError($error, $context);
```

#### Price Calculation
```php
// Use PriceAdjustmentService for price calculations
$newPrice = $this->priceAdjustmentService->calculateAdjustedPrice(
    $basePrice,
    $surchargePercentage
);
```

#### Entity Structure
```php
// PreishoheitProduct entity fields
- id (string, primary)
- productId (string, foreign key)
- active (boolean)
- surchargePercentage (float)
- createdAt (datetime)
- updatedAt (datetime)
```

## Testing

### Running Tests
```bash
# Unit Tests
./vendor/bin/phpunit tests/Unit

# Integration Tests
./vendor/bin/phpunit tests/Integration
```

### Test Coverage
- Unit tests for services and components
- Integration tests for API communication
- End-to-end tests for admin interface
- Price calculation validation
- Error handling verification

## Development

### Directory Structure
```
BOWPreishoheit/
├── src/
│   ├── Controller/
│   │   └── ApiVerificationController.php
│   ├── Exception/
│   │   ├── ApiVerificationException.php
│   │   ├── ConfigurationException.php
│   │   └── PreishoheitApiException.php
│   ├── Service/
│   │   ├── ErrorHandling/
│   │   ├── Price/
│   │   └── PreishoheitApi/
│   └── Resources/
│       └── app/
│           └── administration/
│               ├── component/
│               └── snippet/
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
- Follow reference plugin patterns:
  - FroshTools for tab-based navigation
  - SwagCustomizedProducts for product grid
  - rapi1Connector for API handling

## Support
For support inquiries, please contact:
- Email: support@bow-agentur.de
- Website: https://www.bow-agentur.de
