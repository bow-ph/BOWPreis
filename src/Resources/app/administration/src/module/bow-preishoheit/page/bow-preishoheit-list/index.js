import template from './bow-preishoheit-list.html.twig';
const { Component } = Shopware;
const { Criteria } = Shopware.Data;

Component.register('bow-preishoheit-list', {
    template,

    inject: ['repositoryFactory', 'httpClient'],

    data() {
        return {
            isLoading: false,
            products: [],
            criteria: new Criteria(1, 25),
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

    created() {
        this.loadProducts();
    },

    methods: {
        loadProducts() {
            this.isLoading = true;
            this.criteria.addAssociation('product');

            this.productRepository
                .search(this.criteria, Shopware.Context.api)
                .then(({ items, total }) => {
                    this.products = items;
                    this.total = total;
                })
                .catch((error) => {
                    this.createNotificationError({
                        title: 'Fehler',
                        message: error.message
                    });
                })
                .finally(() => {
                    this.isLoading = false;
                });
        },

        refreshList() {
            this.loadProducts();
        },

        onPageChange({ page = 1, limit = 25 }) {
            this.criteria.page = page;
            this.criteria.limit = limit;
            this.loadProducts();
        },

        onSortColumn({ sortBy, sortDirection }) {
            this.criteria.resetSorting();
            if (sortBy) {
                this.criteria.addSorting(Criteria.sort(sortBy, sortDirection));
            }
            this.loadProducts();
        }
    }
});
