import template from './general-tab.html.twig';
import { Component, Mixin } from 'src/core/shopware';

Component.register('bow-preishoheit-general-tab', {
    template,

    inject: ['BowPreishoheitApiService'],

    mixins: [Mixin.getByName('notification')],

    data() {
        return {
            totalJobs: 0,
            openJobs: 0,
            lastApiCheck: '-',
            isLoading: false
        };
    },

    created() {
        this.loadGeneralInfo();
    },

    methods: {
        loadGeneralInfo() {
            this.isLoading = true;

            this.BowPreishoheitApiService.getGeneralInfo().then(({ data }) => {
                this.totalJobs = data.totalJobs;
                this.openJobs = data.openJobs;
                this.lastApiCheck = data.lastApiCheck;
            }).catch(() => {
                this.createNotificationError({
                    message: 'Failed to load general info.'
                });
            }).finally(() => {
                this.isLoading = false;
            });
        },

        refreshStatus() {
            this.loadGeneralInfo();
        }
    }
});
