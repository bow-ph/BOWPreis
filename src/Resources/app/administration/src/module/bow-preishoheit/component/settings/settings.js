import template from './settings.html.twig';
import { Component, Mixin } from 'src/core/shopware';

Component.register('bow-preishoheit-settings', {
    template,

    inject: ['systemConfigApiService'],

    mixins: [Mixin.getByName('notification')],

    data() {
        return {
            settings: {
                cronInterval: 5,
                overwriteEAN: true,
                overwriteTitle: true,
                overwritePrice: true
            },
            isLoading: false
        };
    },

    created() {
        this.loadSettings();
    },

    methods: {
        loadSettings() {
            this.systemConfigApiService.getValues('BowPreishoheit.settings').then((settings) => {
                this.settings.cronInterval = settings['BowPreishoheit.settings.cronInterval'] || 5;
                this.settings.overwriteEAN = settings['BowPreishoheit.settings.overwriteEAN'] ?? true;
                this.settings.overwriteTitle = settings['BowPreishoheit.settings.overwriteTitle'] ?? true;
                this.settings.overwritePrice = settings['BowPreishoheit.settings.overwritePrice'] ?? true;
            });
        },

        onSave() {
            this.isLoading = true;
            this.systemConfigApiService.saveValues({
                'BowPreishoheit.settings.cronInterval': this.settings.cronInterval,
                'BowPreishoheit.settings.overwriteEAN': this.settings.overwriteEAN,
                'BowPreishoheit.settings.overwriteTitle': this.settings.overwriteTitle,
                'BowPreishoheit.settings.overwritePrice': this.settings.overwritePrice
            }).then(() => {
                this.createNotificationSuccess({
                    message: 'Settings saved successfully.'
                });
            }).catch(() => {
                this.createNotificationError({
                    message: 'Could not save settings.'
                });
            }).finally(() => {
                this.isLoading = false;
            });
        }
    }
});
