import template from './product-grid.html.twig';

const { Component, Mixin } = Shopware;
const { Criteria } = Shopware.Data;
const { Uuid } = Shopware.Utils;

export default {
    template,

    inject: [
        'repositoryFactory',
        'acl'
    ],

    mixins: [
        Mixin.getByName('notification')
    ],

    data() {
        return {
            isLoading: false,
            selectedProducts: [],
            availableProducts: [],
            page: 1,
            limit: 25,
            total: 0,
            sortBy: 'name',
            sortDirection: 'ASC',
            term: '',
            columns: [{
                property: 'name',
                label: this.$tc('bow-preishoheit.grid.columnProduct'),
                primary: true,
                routerLink: 'sw.product.detail'
            }, {
                property: 'productNumber',
                label: this.$tc('bow-preishoheit.grid.columnProductNumber')
            }, {
                property: 'ean',
                label: this.$tc('bow-preishoheit.grid.columnEan')
            }, {
                property: 'preishoheitProduct.surchargePercentage',
                label: this.$tc('bow-preishoheit.grid.columnSurcharge'),
                inlineEdit: 'number'
            }, {
                property: 'preishoheitProduct.active',
                label: this.$tc('bow-preishoheit.grid.columnActive'),
                inlineEdit: 'boolean'
            }]
        };
    },

    computed: {
        productRepository() {
            return this.repositoryFactory.create('product');
        },

        preishoheitProductRepository() {
            return this.repositoryFactory.create('bow_preishoheit_product');
        }
    },

    created() {
        this.loadProducts();
    },

    methods: {
        loadProducts() {
            this.isLoading = true;

            const criteria = new Criteria(this.page, this.limit);
            criteria.addAssociation('preishoheitProduct');
            
            if (this.term) {
                criteria.setTerm(this.term);
            }

            criteria.addSorting(Criteria.sort(this.sortBy, this.sortDirection));

            return this.productRepository.search(criteria)
                .then((result) => {
                    this.availableProducts = result.items;
                    this.total = result.total;
                })
                .catch((error) => {
                    this.createNotificationError({
                        title: this.$tc('bow-preishoheit.grid.errorTitle'),
                        message: error.message
                    });
                })
                .finally(() => {
                    this.isLoading = false;
                });
        },

        onPageChange({ page, limit }) {
            this.page = page;
            this.limit = limit;
            this.loadProducts();
        },

        onSearch(term) {
            this.term = term;
            this.page = 1;
            this.loadProducts();
        },

        onSort({ sortBy, sortDirection }) {
            this.sortBy = sortBy;
            this.sortDirection = sortDirection;
            this.loadProducts();
        },

        async onInlineEditSave(product) {
            try {
                if (!product.preishoheitProduct) {
                    await this.createPreishoheitProduct(product);
                } else {
                    await this.preishoheitProductRepository.save(product.preishoheitProduct);
                }

                this.createNotificationSuccess({
                    title: this.$tc('bow-preishoheit.grid.successTitle'),
                    message: this.$tc('bow-preishoheit.grid.successMessage')
                });
            } catch (error) {
                this.createNotificationError({
                    title: this.$tc('bow-preishoheit.grid.errorTitle'),
                    message: error.message
                });
            }
        },

        onSelectionChange(selection) {
            this.selectedProducts = Object.values(selection);
        },

        async createPreishoheitProduct(product) {
            const preishoheitProduct = this.preishoheitProductRepository.create();
            preishoheitProduct.id = Uuid.randomHex();
            preishoheitProduct.productId = product.id;
            preishoheitProduct.active = true;
            preishoheitProduct.surchargePercentage = 0;
            
            await this.preishoheitProductRepository.save(preishoheitProduct);
            await this.loadProducts();
        },

        onAddProducts() {
            if (!this.selectedProducts.length) {
                return;
            }

            const newProducts = this.selectedProducts.map(product => {
                return {
                    id: Uuid.randomHex(),
                    productId: product.id,
                    active: true,
                    surchargePercentage: 0
                };
            });

            this.isLoading = true;
            return this.preishoheitProductRepository.sync(newProducts)
                .then(() => {
                    this.createNotificationSuccess({
                        title: this.$tc('bow-preishoheit.grid.successTitle'),
                        message: this.$tc('bow-preishoheit.grid.successMessage')
                    });
                    this.loadProducts();
                })
                .catch((error) => {
                    this.createNotificationError({
                        title: this.$tc('bow-preishoheit.grid.errorTitle'),
                        message: error.message
                    });
                })
                .finally(() => {
                    this.isLoading = false;
                });
        }
    }
};
