import { startCase } from 'lodash';
import Vue from 'vue';
import Vuex from 'vuex';

Vue.use(Vuex);

export default new Vuex.Store({
    state: {
        appError: {
            errorType: null,
            errorMsg: null,
        },
        user: null,
        store: null,
        cart: {
            totalQuantity: null,
        },
        pages: [],
        pagesLoading: false,

        categories: [],
        categoriesLoading: false,

        products: [],
        productsLoading: false,

        menu: [],
        menuLoading: false,
        mobileMenu: [],
        menuMobileLoading: false,
    },
    mutations: {
        setAppError(state, appError) {
            state.appError = {...appError};
        },
        clearAppError(state) {
            state.appError.errrorType =  null;
            state.appError.msg = null;
        },
        updateCartTotalQuantity(state, totalQuantity) {
            state.cart.totalQuantity = totalQuantity;
        },
        setUser(state, user) {
            state.user = user;
        },
        setStore(state, store) {
            state.store = store;
        },
        // Store Page State
        addPage(state, page) {
            state.pages.push(page);
        },
        setPagesLoading(state, loading) {
            state.pagesLoading = loading;
        },

        // Category State
        addCategory(state, category) {
            state.categories.push(category);
        },
        setCategoriesLoading(state, loading) {
            state.categoriesLoading = loading;
        },

        // Product State
        addProduct(state, product) {
            state.products.push(product);
        },
        setProductsLoading(state, loading) {
            state.productsLoading = loading;
        },
        
        // Menu
        setMenu(state, menu) {
            state.menu = menu;
        },
        setMenuLoading(state, loading) {
            state.menuLoading = loading;
        },

        // Mobile Menu
        setMobileMenu(state, menu) {
            state.mobileMenu = menu;
        },
        setMenuMobileLoading(state, loading) {
            state.menuMobileLoading = loading;
        }
    },
    actions: {
        setAppError(context, appError) {
            this.commit('setAppError', appError);
        },
        clearAppError(context) {
            this.commit('clearAppError');
        },
        async getCartTotalQuantity(context, storeId) {
            const result = await this._vm.$api.cartTotalQuantity(storeId);
            this.commit('updateCartTotalQuantity', result['total_quantity']);
        },
        setUser(context, user) {
            context.commit('setUser', user);
        },
        setStore(context, store) {
            context.commit('setStore', store);
        },
        async loadPageBySlug(context, slug = null) {
            console.log('vuex - loadPageBySlug()', slug);
            try {
                context.commit('setPagesLoading', true);
                let page = null;
                if (slug && slug != '/') {
                    page = await this._vm.$api.getPage(this.state.store.id, slug);
                } else {
                    page = await this._vm.$api.getHomePage(this.state.store.id);
                }
                context.commit('addPage', page);
                return page;
            }
            catch(error) {
                throw error;
            }
            finally {
                context.commit('setPagesLoading', false);
            }
        },
        async loadCategory(context, id) {
            try {
                context.commit('setCategoriesLoading', true);
                let category = await this._vm.$api.getCategory(this.state.store.id, id);
                context.commit('addCategory', category);
                return category;
                
            }
            catch(error) {
                throw error;
            }
            finally {
                context.commit('setCategoriesLoading', false);
            }
        },
        async loadProduct(context, id) {
            try {
                context.commit('setProductsLoading', true);
                let product = await this._vm.$api.getProduct(this.state.store.id, id);
                context.commit('addProduct', product);
                return product;
            } catch (error) {
                throw error;
            } finally {
                context.commit('setProductsLoading', false);
            }
        },
        async loadProducts(context) {
            try {
                context.commit('setProductsLoading', true);
                let products = await this._vm.$api.getProducts(this.state.store.id);
                return products;
            } catch (error) {
                throw error;
            } finally {
                context.commit('setProductsLoading', false);
            }
        },
        async loadMenu(context) {
            try {
                context.commit('setMenuLoading', true);
                let menu = await this._vm.$api.getStoreMenu(this.state.store.id);
                context.commit('setMenu', menu);
                return menu;
            }
            catch(error) {
                throw error;
            } finally {
                context.commit('setMenuLoading', false);
            }
        },
        async loadMobileMenu(context) {
            try {
                context.commit('setMenuMobileLoading', true);
                let mobileMenu = await this._vm.$api.getStoreMobileMenu(this.state.store.id);
                context.commit('setMobileMenu', mobileMenu);
                return mobileMenu;
            }
            catch(error) {
                throw error;
            } finally {
                context.commit('setMenuMobileLoading', false);
            }
        },
    },
    getters: {
        getPageByID: (state) => (id) => {
            return state.pages.find( page => page.id == id);
        },
        getPageBySlug: (state) => (slug) => {
            // Handle null slug for home page
            if(slug === null ){
                return state.pages.find(page => page.slug === '/');
            } else {
                return state.pages.find(page => page.slug === slug);
            }
        },
        getCategoryByID: (state) => (id) => {
            return state.categories.find( category => category.id == id);
        },
        getProductByID: (state) => (id) => {
            return state.products.find(product => product.id == id);
        },
    },
});
