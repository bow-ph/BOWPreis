import template from './api-config.html.twig';

const { Component } = Shopware;

export default {
    template,

    inject: [
        'systemConfigApiService'
    ],

    data() {
        return {
            isLoading: false,
            isSaveSuccessful: false,
            isVerifying: false,
            validationError: null,
            config: {
                'BOWPreishoheit.config.apiKey': ''
            }
        };
    },

    created() {
        this.loadConfig();
    },

    methods: {
        loadConfig() {
            this.isLoading = true;
            return this.systemConfigApiService
                .getValues('BOWPreishoheit.config')
                .then((values) => {
                    this.config = values;
                })
                .finally(() => {
                    this.isLoading = false;
                });
        },

        saveConfig() {
            this.isLoading = true;
            this.validationError = null;
            
            if (!this.config['BOWPreishoheit.config.apiKey']) {
                this.validationError = this.$tc('bow-preishoheit.apiConfig.apiKeyRequired');
                this.isLoading = false;
                return Promise.reject(new Error(this.validationError));
            }

            return this.systemConfigApiService
                .saveValues(this.config)
                .then(() => {
                    this.isSaveSuccessful = true;
                    return this.verifyApiKey();
                })
                .catch((error) => {
                    this.validationError = error.message;
                    this.createNotificationError({
                        title: this.$tc('bow-preishoheit.apiConfig.errorTitle'),
                        message: error.message
                    });
                    throw error;
                })
                .finally(() => {
                    this.isLoading = false;
                });
        },

        verifyApiKey() {
            this.isVerifying = true;
            this.validationError = null;
            
            return this.systemConfigApiService
                .getValues('BOWPreishoheit.config')
                .then((values) => {
                    if (!values['BOWPreishoheit.config.apiKey']) {
                        throw new Error(this.$tc('bow-preishoheit.apiConfig.apiKeyRequired'));
                    }
                    
                    return this.$http.post(
                        `${this.getApplicationRootPath()}/api/_action/bow-preishoheit/verify-api-key`,
                        { apiKey: values['BOWPreishoheit.config.apiKey'] }
                    );
                })
                .then((response) => {
                    if (response.data.success) {
                        this.validationError = null;
                        this.createNotificationSuccess({
                            title: this.$tc('bow-preishoheit.apiConfig.verificationSuccessTitle'),
                            message: this.$tc('bow-preishoheit.apiConfig.verificationSuccessMessage')
                        });
                    } else {
                        throw new Error(response.data.message || this.$tc('bow-preishoheit.apiConfig.verificationErrorMessage'));
                    }
                })
                .catch((error) => {
                    this.validationError = error.message;
                    this.createNotificationError({
                        title: this.$tc('bow-preishoheit.apiConfig.verificationErrorTitle'),
                        message: error.message
                    });
                    throw error;
                })
                .finally(() => {
                    this.isVerifying = false;
                });
        }
    }
};
