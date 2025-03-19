import template from './bow-preishoheit-list.html.twig';

const { Component, Mixin } = Shopware;
const { Criteria } = Shopware.Data;

Component.register('bow-preishoheit-list', {
    template,

    inject: [
        'repositoryFactory'
    ],

    data() {
        return {
            isLoading: false,
            products: null,
            criteria: new Criteria(),
            total: 0
        };
    },

    computed: {
        productRepository() {
            return this.repositoryFactory.create('bow_preishoheit_product');
        },

        productColumns() {
            return [
                {
                    property: 'product.name',
                    label: this.$tc('bow-preishoheit.list.columnProductName'),
                    routerLink: 'bow.preishoheit.detail',
                    primary: true
                },
                {
                    property: 'product.ean',
                    label: this.$tc('bow-preishoheit.list.columnEan')
                },
                {
                    property: 'product.price',
                    label: this.$tc('bow-preishoheit.list.columnPrice')
                },
                {
                    property: 'synchronizedAt',
                    label: this.$tc('bow-preishoheit.list.columnLastUpdate')
                }
            ];
        }
    },

    data() {
        return {
            isLoading: false,
            products: [],
            criteria: new Criteria(1, 25),
            total: 0
        };
    },

    created() {
        this.loadProducts();
    },

    methods: {
        loadProducts() {
            this.isLoading = true;

            this.criteria.addAssociation('product');

            this.repositoryFactory.create('bow_preishoheit_product')
                .search(this.criteria, Shopware.Context.api)
                .then(({ items, total }) => {
                    this.products = items;
                    this.total = total;
                })
                .finally(() => {
                    this.isLoading = false;
                });
    },

        refreshList() {
            this.loadProducts();
        }
    }
});
