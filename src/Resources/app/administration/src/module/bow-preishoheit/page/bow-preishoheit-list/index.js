import template from './bow-preishoheit-list.html.twig';

const { Component } = Shopware;
const { Criteria } = Shopware.Data;

export default {
    template,

    inject: [
        'repositoryFactory'
    ],

    data() {
        return {
            isLoading: false,
            products: null,
            total: 0,
            criteria: new Criteria(1, 25)
        };
    },

    computed: {
        productRepository() {
            return this.repositoryFactory.create('bow_preishoheit_product');
        },

        productColumns() {
            return [{
                property: 'product.name',
                label: this.$tc('bow-preishoheit.list.columnProductName'),
                routerLink: 'bow.preishoheit.detail',
                primary: true
            }, {
                property: 'product.ean',
                label: this.$tc('bow-preishoheit.list.columnEan')
            }, {
                property: 'surchargePercentage',
                label: this.$tc('bow-preishoheit.list.columnSurcharge')
            }, {
                property: 'discountPercentage',
                label: this.$tc('bow-preishoheit.list.columnDiscount')
            }, {
                property: 'updatedAt',
                label: this.$tc('bow-preishoheit.list.columnLastUpdate')
            }];
        }
    },

    created() {
        this.getList();
    },

    methods: {
        getList() {
            this.isLoading = true;

            this.criteria.addAssociation('product');

            return this.productRepository.search(this.criteria)
                .then(({ items, total }) => {
                    this.products = items;
                    this.total = total;
                })
                .finally(() => {
                    this.isLoading = false;
                });
        },

        onRefresh() {
            this.getList();
        }
    }
};
