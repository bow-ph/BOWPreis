import enGB from './module/bow-preishoheit/snippet/en-GB.json';
import deDE from './module/bow-preishoheit/snippet/de-DE.json';

// Register snippets before module
Shopware.Application.addServiceProviderDecorator('snippetService', (service) => {
    service.extend('en-GB', enGB);
    service.extend('de-DE', deDE);
    return service;
});

// Import module after snippets
import './module/bow-preishoheit';
