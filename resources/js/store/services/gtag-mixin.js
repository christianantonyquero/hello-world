// Wrapper Around Google anlyatics
export default {
    gaPageView(event_params = {}) {
        try {
            gtag('event', 'page_view', event_params);
        }
        catch(error) {
            console.error('gtag page_view error')
            console.error(error);
        }
    },

    gaEvent(action, event_params={}) {
        try {
            gtag('event', action, event_params);
        }
        catch (error) {
            console.error('gtag event error')
            console.error(error);
        }
    },

    gaAddToCart(items) {
        this.gaEvent('add_to_cart', {'items':items});
    },

    gaViewProduct(items) {
        this.gaEvent('view_item', {'items':items});
    },

    gaViewProducts(items) {
        this.gaEvent('view_items', {'items':items});
    },

    gaBeginCheckout() {
        this.gaEvent('begin_checkout');
    },
}