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

// Importiere den API-Service für die Dependency Injection
import BowPreishoheitApiService from './module/bow-preishoheit/service/api.service';

// Dependency Injection des API-Services für einfache Nutzung in Komponenten
Shopware.Application.addServiceProvider('BowPreishoheitApiService', (container) => {
    const initContainer = Shopware.Application.getContainer('init');
    return new BowPreishoheitApiService(initContainer.httpClient, container.loginService);
});
