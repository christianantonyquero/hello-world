import Vue from 'vue'
import Router from 'vue-router'
import vuexStore from '../vuex-store';

// Route Components
import StorePage from '../pages/StorePage.vue'
import StoreCategoryProductPage from '../pages/StoreCategoryProductPage.vue'
import Cart from '../pages/Cart.vue'
import Products from '../pages/Products.vue'
import ErrorPage from '../pages/ErrorPage.vue';

import ProductListing from '../components/ProductListing.vue';
import ProductDetail from  '../components/ProductDetail.vue';

import PhotoBooth from '../pages/PhotoBooth.vue';

// Google Analytics / Tag Helpers
import gtagHelpers from '../services/gtag-mixin';

// Fix router exception on redirect or cancel next
//https://github.com/vuejs/vue-router/issues/2881#issuecomment-520554378
const originalPush = Router.prototype.push

Router.prototype.push = function push(location, onResolve, onReject) {
    if (onResolve || onReject) {
        return originalPush.call(this, location, onResolve, onReject)
    } else {
        return originalPush.call(this, location).catch((err) => {
            if (Router.isNavigationFailure(err)) {
                //resolve err
                return err
            }
            // rethrow error
            return Promise.reject(err)
        })
    }
}

Vue.use(Router);
const ExampleComponent = { template: "<div>Example</div>" };
const routes = [
  {
      path: '',    // this gets loaded implicily b/c of the / route
      name: 'Home',
      component: StorePage,
      props: true,
      alias: '/%2F'
  },
  {
      path: '/category/:category_id',
      component: StoreCategoryProductPage,
      children: [
          { path: '', component: ProductListing, name: 'ProductListing' },
      ],
  },
  {
      path: '/product/:product_id',
      component: StoreCategoryProductPage,
      children: [
          {
              path: '',
              component: ProductDetail,
              name: 'ProductDetail'
          }
      ],
  },
  {
      path: '/cart',
      name: 'Cart',
      component: Cart,
  },
  {
      path: '/products',
      name: 'Products',
      component: Products,
  },
  {
      path: '/error',
      name: 'Error',
      component: ErrorPage,
  },
  {
      path: '/photo-booth',
      name: 'PhotoBooth',
      component: PhotoBooth,
      meta: {
          requiresAuth: true
      }
  },
  {
      path: '/:slug',
      name: 'Page',
      component: StorePage,
      props: true,
  },
];

const router = new Router({
    routes,
});

// Navigation Guard
// - beforeResolve fires later. hopefully stuff is loaded at the app level
router.beforeEach((to, from, next) => {
    // https://router.vuejs.org/guide/advanced/meta.html
    // not sure why we search matched. I guess to catch nested routes??
    // Simple Auth check, do we have a user object in the VUEX store
    if(to.matched.some(record => record.meta.requiresAuth) && vuexStore.state.user === null) {
        vuexStore.dispatch('setAppError', {
            type: 'access-denied',
            msg: 'Access Denied. You do not have permission to access that page.'
        });
        // global app variable seems to work for accessing vue
        next({name: 'Error'});
    } else {
        next();
    }
})

// After Each Route
router.afterEach((to) => {
    gtagHelpers.gaPageView({
        page_location: document.origin + to.fullPath,
        page_path: to.fullPath,
        page_title: document.title,
    });
});

export default router;
