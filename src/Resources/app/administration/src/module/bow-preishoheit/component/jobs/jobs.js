import template from './jobs-tab.html.twig';
import { Component, Mixin } from 'src/core/shopware';

Component.register('bow-preishoheit-jobs-tab', {
    template,

    inject: ['BowPreishoheitApiService'],

    mixins: [Mixin.getByName('notification')],

    data() {
        return {
            job: {
                ean: '',
                country: '',
                platform: '',
                category: '',
                products: []
            },
            countries: [{ value: 'DE', label: 'Germany' }, { value: 'AT', label: 'Austria' }],
            platforms: [{ value: 'amazon', label: 'Amazon' }, { value: 'ebay', label: 'eBay' }],
            categories: [{ value: 'electronics', label: 'Electronics' }, { value: 'home', label: 'Home' }],
            isLoading: false
        };
    },

    methods: {
        createJob() {
            this.isLoading = true;

            this.BowPreishoheitApiService.createJob(this.job).then(() => {
                this.createNotificationSuccess({message: 'Job successfully created!'});
                this.resetForm();
            }).catch(() => {
                this.createNotificationError({message: 'Failed to create job!'});
            }).finally(() => {
                this.isLoading = false;
            });
        },

        resetForm() {
            this.job = {
                ean: '',
                country: '',
                platform: '',
                category: '',
                products: []
            };
        }
    }
});
