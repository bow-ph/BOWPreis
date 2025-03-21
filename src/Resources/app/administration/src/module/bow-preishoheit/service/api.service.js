import ApiService from 'src/core/service/api.service';

class BowPreishoheitApiService extends ApiService {
    constructor(httpClient, loginService, apiEndpoint = 'bow-preishoheit') {
        super(httpClient, loginService, apiEndpoint);
    }

    getGeneralInfo() {
        return this.httpClient.get('/api/bow-preishoheit/general-info', {
            headers: this.getBasicHeaders()
        });
    }

    testApiKey() {
        return this.httpClient.post('/api/bow-preishoheit/config/test-api-key', {}, {
            headers: this.getBasicHeaders()
        });
    }

    createJob(payload) {
        return this.httpClient.post('/api/bow-preishoheit/job', payload, {
            headers: this.getBasicHeaders()
        });
    }

    getResults() {
        return this.httpClient.get('/api/bow-preishoheit/results', {
            headers: this.getBasicHeaders()
        });
    }
    
    updateResult(resultData) {
        return this.httpClient.post(`/api/bow-preishoheit/results/${resultData.jobId}`, resultData, {
            headers: this.getBasicHeaders()
        });
    }
    
    getMappings() {
        return this.httpClient.get('/api/bow-preishoheit/mapping', {
            headers: this.getBasicHeaders()
        });
    }
    
    createMapping(mappingData) {
        return this.httpClient.post('/api/bow-preishoheit/mapping', mappingData, {
            headers: this.getBasicHeaders()
        });
    }
    
    deleteMapping(mappingId) {
        return this.httpClient.delete(`/api/bow-preishoheit/mapping/${mappingId}`, {
            headers: this.getBasicHeaders()
        });
    }

    saveApprovedResults(results) {
        return this.httpClient.post('/api/bow-preishoheit/results/save-approved', results, {
            headers: this.getBasicHeaders()
        });
    }
    
    
    
}

export default BowPreishoheitApiService;
