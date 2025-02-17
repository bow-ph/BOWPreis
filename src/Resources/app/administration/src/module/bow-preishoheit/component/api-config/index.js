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
            return this.systemConfigApiService
                .saveValues(this.config)
                .then(() => {
                    this.isSaveSuccessful = true;
                })
                .catch((error) => {
                    this.createNotificationError({
                        title: this.$tc('bow-preishoheit.apiConfig.errorTitle'),
                        message: error.message
                    });
                })
                .finally(() => {
                    this.isLoading = false;
                });
        },

        verifyApiKey() {
            this.isVerifying = true;
            return this.systemConfigApiService
                .getValues('BOWPreishoheit.config')
                .then((values) => {
                    return this.$http.post(
                        `${this.getApplicationRootPath()}/api/_action/bow-preishoheit/verify-api-key`,
                        { apiKey: values['BOWPreishoheit.config.apiKey'] }
                    );
                })
                .then((response) => {
                    if (response.data.success) {
                        this.createNotificationSuccess({
                            title: this.$tc('bow-preishoheit.apiConfig.verificationSuccessTitle'),
                            message: this.$tc('bow-preishoheit.apiConfig.verificationSuccessMessage')
                        });
                    } else {
                        throw new Error(response.data.message || this.$tc('bow-preishoheit.apiConfig.verificationErrorMessage'));
                    }
                })
                .catch((error) => {
                    this.createNotificationError({
                        title: this.$tc('bow-preishoheit.apiConfig.verificationErrorTitle'),
                        message: error.message
                    });
                })
                .finally(() => {
                    this.isVerifying = false;
                });
        }
    }
};
