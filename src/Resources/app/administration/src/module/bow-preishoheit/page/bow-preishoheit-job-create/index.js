import template from './bow-preishoheit-job-create.html.twig';

const { Component, Data: { Criteria } } = Shopware;

Component.register('bow-preishoheit-job-create', {
    template,

    inject: ['systemConfigApiService', 'repositoryFactory', 'httpClient'],

    mixins: [
        Shopware.Mixin.getByName('notification')
    ],

    data() {
        return {
            isLoading: false,
            jobData: {
                productGroup: '',
                selectedProducts: [],
                dynamicProductGroupId: null,
                countries: [],
                categories: []
            },
            config: {},
            countries: [],
            dynamicProductGroups: [],
            productGroups: [
                { label: 'Amazon', value: 'amazon' },
                { label: 'Google Shopping', value: 'google-shopping' }
            ],
        };
    },

    created() {
        this.loadConfig();
        this.loadCountries();
        this.loadDynamicProductGroups();
    },

    methods: {
        loadConfig() {
            this.isLoading = true;
            this.systemConfigApiService.getValues('BOWPreishoheit.config')
                .then(values => {
                    this.config = values;
                })
                .finally(() => {
                    this.isLoading = false;
                });
        },

        loadCountries() {
            const countryRepo = this.repositoryFactory.create('country');
            countryRepo.search(new Criteria(), Shopware.Context.api)
                .then(result => {
                    this.countries = result.items.map(country => ({
                        label: country.name,
                        value: country.iso
                    }));
                });
        },

        loadDynamicProductGroups() {
            const dynamicGroupRepo = this.repositoryFactory.create('product_stream');
            dynamicGroupRepo.search(new Criteria(), Shopware.Context.api)
                .then(result => {
                    this.dynamicProductGroups = result.items.map(group => ({
                        label: group.name,
                        value: group.id
                    }));
                });
        },

        saveJob() {
            this.isLoading = true;

            const identifiers = this.jobData.selectedProducts
                .map(product => product.ean)
                .filter(Boolean);

            const payload = {
                productGroup: this.jobData.productGroup,
                identifiers,
                countries: this.jobData.countries,
                categories: this.jobData.categories,
                dynamicProductGroupId: this.jobData.dynamicProductGroupId
            };

            this.httpClient.post('/api/_action/bow-preishoheit/jobs/create', payload)
                .then(response => {
                    this.createNotificationSuccess({
                        title: this.$tc('bow-preishoheit.jobs.successTitle'),
                        message: this.$tc('bow-preishoheit.jobs.createSuccess')
                    });
                    this.$router.push({ name: 'bow.preishoheit.jobList' });
                })
                .catch(error => {
                    this.createNotificationError({
                        title: this.$tc('bow-preishoheit.jobs.errorTitle'),
                        message: error.response?.data?.message || error.message
                    });
                })
                .finally(() => {
                    this.isLoading = false;
                });
        },

        onCancel() {
            this.$router.push({ name: 'bow.preishoheit.jobList' });
        }
    }
});
