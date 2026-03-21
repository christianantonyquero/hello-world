import axios from 'axios';

// TODO: possibly refactor this to use a request() method in this class?
// TODO: could also intercept router requests to pages protected by "line" concept.
//       api-based handler wouldn't work for pages with no API calls
// TODO: handle un-authenticated requests here also?
axios.interceptors.response.use(function (response) {
    return response;
  }, function (error) {
    
    let error_status = error.response.status;
    let error_msg = error.response.data.error;
    
    if (error_status == 403) {
      if( error.response.data.error == 'Age Gate Access Denied' ) {
        console.log('Unauthorized - Age Gate');
        
        window.app.$store.commit("setAppError", {
            errorType: 'age-gate-denined',
            errorMsg: error_msg
        });
        console.log('Age Gate Error')
        window.app.$router.push({name: 'Error'});

      } else {
        console.log('Error Not authorized');
        console.log(error.response.data.error);
        
        window.app.$store.commit("setAppError", {
            errorType: 'not-authorized',
            errorMsg: error_msg
        });

        window.app.$router.push({name: 'Error'});
      }
    } else if (error_status == 401) {
        console.log('Unauthenticated');
        window.location.reload(); // reload to trigger login
    }
    return Promise.reject(error);
});

export default {
  getProducts(storeId){
    return axios.get('/store/' + storeId + '/api/products').then(response => {
        return response.data;
    });
  },
  getSuggested(storeId){
    return axios.get('/store/' + storeId + '/api/products/suggested').then(response => {
        return response.data;
    });
  },
  async getStoreMenu(storeId) {
    const response = await axios.get(`/store/${storeId}/api/menu`);
    return response.data;
  },
  async getStoreMobileMenu(storeId) {
    const response = await axios.get(`/store/${storeId}/api/mobile_menu`);
    return response.data;
  },
  async getCategory(storeId, categoryId) {
    const response = await axios.get(`/store/${storeId}/api/categories/${categoryId}`);
    return response.data;
  },
  async getProduct(storeId, productId){
    const response = await axios.get(`/store/${storeId}/api/products/${productId}`);
    return response.data;
  },

  async getHomePage(storeId) {
    const response = await axios.get(`/store/${storeId}/api/pages/load/home`);
    return response.data;
  },

  async getPage(storeId, slug){
    const response =  await axios.get(`/store/${storeId}/api/pages/load/${slug}`);
    return response.data;
  },

  async getStoreAudio(storeId) {
    const response = await axios.get(`/store/${storeId}/api/songs`);
    return response.data;
  },

  // Photo Booth
  async getPhotoBoothBackdrops(storeId) {
    const response = await axios.get(`/store/${storeId}/api/photo-booth/backdrops`);
    return response.data.backdrops;
  },

  async photoBoothProcessUserPhoto(storeId, formData) {
      const response = await axios.post(`/store/${storeId}/api/photo-booth/process-user-image`, formData, {
          headers: {
              "Content-Type": "multipart/form-data"
          }
      });
      return response.data;
  },

   async photoBoothSaveUserPhoto(storeId, formData) {
       const response = await axios.post(`/store/${storeId}/api/photo-booth/save-user-image`, formData, {
           headers: {
               "Content-Type": "multipart/form-data"
           }
       });
       return response.data;
   },

  /*
   * Cart
   */
  addProductToCart(storeId, productId, quantity, sizeId){
    console.log("DEBUG 4 addProductToCart Function");
    return axios.post('/store/' + storeId + '/api/cart/add', {
      'product_id' : productId,
      'quantity' : quantity,
      'size_id' : sizeId,
    }).then(response => {
        console.log("DEBUG 5", response);
        return response.data;
    });
  },

  // Clear Cart
  async clearCart(storeId) {
      const response = await axios.delete('/store/' + storeId + '/api/cart/clear');
      return response.data;
  },

  // Get Cart Total Quantity
  async cartTotalQuantity(storeId) {
      const response = await axios.get('/store/' + storeId + '/api/cart/total-quantity');
      return response.data;
  },

  /*
   * Orders/Checkout
   */
  getCartOrder(storeId){
    return axios.get('/store/' + storeId + '/api/checkout').then(response => {
        return response.data;
    });
  },
}
