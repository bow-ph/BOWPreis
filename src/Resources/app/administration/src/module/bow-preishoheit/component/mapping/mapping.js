import template from './mapping-tab.html.twig';
import { Component, Mixin } from 'src/core/shopware';

Component.register('bow-preishoheit-mapping-tab', {
    template,

    inject: ['BowPreishoheitApiService'],

    mixins: [Mixin.getByName('notification')],

    data() {
        return {
            mapping: {
                productId: null,
                externalId: ''
            },
            mappings: [],
            isLoading: false,
            columns: [
                { property: 'productName', label: 'Product Name' },
                { property: 'externalId', label: 'External ID' },
                { property: 'actions', label: 'Actions', allowResize: false }
            ]
        };
    },

    created() {
        this.loadMappings();
    },

    methods: {
        loadMappings() {
            this.BowPreishoheitApiService.getMappings().then(({ data }) => {
                this.mappings = data;
            });
        },

        createMapping() {
            this.isLoading = true;

            this.BowPreishoheitApiService.createMapping(this.mapping).then(() => {
                this.createNotificationSuccess({ message: 'Mapping saved successfully!' });
                this.loadMappings();
                this.resetForm();
            }).catch(() => {
                this.createNotificationError({ message: 'Failed to save mapping!' });
            }).finally(() => {
                this.isLoading = false;
            });
        },

        deleteMapping(item) {
            this.BowPreishoheitApiService.deleteMapping(item.id).then(() => {
                this.createNotificationSuccess({ message: 'Mapping deleted!' });
                this.loadMappings();
            }).catch(() => {
                this.createNotificationError({ message: 'Failed to delete mapping!' });
            });
        },

        resetForm() {
            this.mapping.productId = null;
            this.mapping.externalId = '';
        }
    }
});
