import template from './bow-preishoheit-job-create.html.twig';

const { Component } = Shopware;

Component.register('bow-preishoheit-job-create', {
    template,

    inject: ['systemConfigApiService', 'repositoryFactory'],

    data() {
        return {
            isLoading: false,
            jobData: {
                productGroup: '',
                identifiers: '',
                countries: [],
                categories: []
            },
            config: {},
            countries: [],
            productGroups: [
                { label: 'Amazon', value: 'amazon' },
                { label: 'Google Shopping', value: 'google-shopping' }
            ],
        };
    },

    created() {
        this.loadConfig();
        this.loadCountries();
    },

    methods: {
        loadConfig() {
            this.isLoading = true;
            this.systemConfigApiService.getValues('BOWPreishoheit.config')
                .then((values) => {
                    this.config = values;
                })
                .finally(() => {
                    this.isLoading = false;
                });
        },
    
        loadCountries() {
            const countryRepo = this.repositoryFactory.create('country');
            countryRepo.search(new Shopware.Data.Criteria(), Shopware.Context.api)
                .then(result => {
                    this.countries = result;
                });
        },
    
        loadDynamicProductGroups() {
            const dynamicGroupRepo = this.repositoryFactory.create('product_stream');
            dynamicGroupRepo.search(new Shopware.Data.Criteria(), Shopware.Context.api)
                .then(result => {
                    this.dynamicProductGroups = result;
                });
        },
    
        saveJob() {
            this.isLoading = true;
    
            const identifiers = this.jobData.selectedProducts.map(product => product.ean).filter(Boolean);
    
            const payload = {
                productGroup: this.jobData.productGroup,
                identifiers: identifiers,
                countries: this.jobData.countries,
                categories: this.jobData.categories,
                dynamicProductGroupId: this.jobData.dynamicProductGroupId
            };
    
            this.$http.post('/api/_action/bow-preishoheit/jobs/create', payload)
                .then(response => {
                    if (response.data.success) {
                        this.createNotificationSuccess({
                            title: this.$tc('bow-preishoheit.jobs.successTitle'),
                            message: this.$tc('bow-preishoheit.jobs.createSuccess')
                        });
                        this.$router.push({ name: 'bow.preishoheit.jobList' });
                    } else {
                        this.createNotificationError({
                            title: this.$tc('bow-preishoheit.jobs.errorTitle'),
                            message: response.data.message
                        });
                    }
                })
                .catch(error => {
                    this.createNotificationError({
                        title: this.$tc('bow-preishoheit.jobs.errorTitle'),
                        message: error.message
                    });
                })
                .finally(() => {
                    this.isLoading = false;
                });
        },
    
        onCancel() {
            this.$router.push({ name: 'bow.preishoheit.jobList' });
        },
    
        onProductSelection(products) {
            this.jobData.selectedProducts = products;
        }
    }
    
});
