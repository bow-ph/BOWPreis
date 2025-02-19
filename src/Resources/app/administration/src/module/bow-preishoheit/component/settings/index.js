import template from './settings.html.twig';
import './settings.scss';

const { Component, Mixin } = Shopware;

Component.register('bow-preishoheit-settings', {
    template,

    inject: [
        'repositoryFactory'
    ],

    mixins: [
        Mixin.getByName('notification')
    ],

    props: {
        product: {
            type: Object,
            required: true
        }
    },

    data() {
        return {
            isLoading: false,
            isUpdating: false
        };
    },

    methods: {
        onSave() {
            this.$emit('save');
        },

        async onManualUpdate() {
            this.isUpdating = true;

            try {
                await this.$http.post(
                    `${this.getApplicationRootPath()}/api/_action/bow-preishoheit/update-prices`
                );

                this.createNotificationSuccess({
                    title: this.$tc('bow-preishoheit.settings.successTitle'),
                    message: this.$tc('bow-preishoheit.settings.updateSuccess')
                });
            } catch (error) {
                this.createNotificationError({
                    title: this.$tc('bow-preishoheit.settings.errorTitle'),
                    message: error.response?.data?.message || this.$tc('bow-preishoheit.settings.updateError')
                });
            } finally {
                this.isUpdating = false;
            }
        }
    }
});
