import template from './bow-preishoheit-preview.html.twig';
import './bow-preishoheit-preview.scss';

const { Component } = Shopware;

Component.register('bow-preishoheit-preview', {
    template,

    inject: [
        'systemConfigApiService'
    ],

    data() {
        return {
            isLoading: false,
            previewData: [],
            productGroup: null,
            selectedCountries: [],
            productIdentifiers: [],
            config: {}
        };
    },

    created() {
        this.loadConfig();
    },

    methods: {
        loadConfig() {
            this.isLoading = true;

            this.systemConfigApiService.getValues('BOWPreishoheit.config')
                .then((values) => {
                    this.config = values;
                    this.productGroup = this.config['BOWPreishoheit.config.productGroup'] || 'amazon';
                    this.selectedCountries = this.config['BOWPreishoheit.config.countrySelection'] || [];
                })
                .catch(() => {
                    this.createNotificationError({
                        title: this.$tc('bow-preishoheit.preview.errorTitle'),
                        message: this.$tc('bow-preishoheit.preview.configLoadError')
                    });
                })
                .finally(() => {
                    this.isLoading = false;
                });
        },

        fetchPreviewData() {
            if (!this.productGroup || !this.selectedCountries.length) {
                this.createNotificationError({
                    title: this.$tc('bow-preishoheit.preview.errorTitle'),
                    message: this.$tc('bow-preishoheit.preview.missingConfigError')
                });
                return;
            }

            this.isLoading = true;

            const payload = {
                productGroup: this.productGroup,
                countries: this.selectedCountries,
                identifiers: this.productIdentifiers
            };

            return this.$http.post('/api/_action/bow-preishoheit/preview', payload)
                .then(response => {
                    if (response.data.success) {
                        this.previewData = response.data.data;
                    } else {
                        this.createNotificationError({
                            title: this.$tc('bow-preishoheit.preview.errorTitle'),
                            message: response.data.message || this.$tc('bow-preishoheit.preview.loadError')
                        });
                    }
                })
                .catch((error) => {
                    this.createNotificationError({
                        title: this.$tc('bow-preishoheit.preview.errorTitle'),
                        message: error.message || this.$tc('bow-preishoheit.preview.loadError')
                    });
                })
                .finally(() => {
                    this.isLoading = false;
                });
        }
    }
});
