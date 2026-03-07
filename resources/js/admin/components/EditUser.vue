<template>
  <form id="edit-user-app" @submit="updateUser" action="/admin/user" method="post">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-body">
          <div v-show="user">
            <div class="row">
              <div class="col-md">
                <h2>Edit User</h2>
              </div>
            </div>
            <hr />
            <errors v-bind:errors="errors"></errors>
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label for="edit_user_name">Name</label>
                  <input
                    type="text"
                    class="form-control"
                    id="edit_user_name"
                    name="edit_user_name"
                    v-model="user.name"
                    aria-describedby="name"
                    placeholder="Name"
                    required
                  />
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="edit_user_email">Email</label>
                  <input
                    type="text"
                    class="form-control"
                    id="edit_user_email"
                    name="edit_user_email"
                    v-model="user.email"
                    aria-describedby="email"
                    placeholder="Email"
                    required
                  />
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label for="edit_password">Password</label>
                  <input
                    type="password"
                    class="form-control"
                    id="edit_password"
                    name="edit_password"
                    v-model="user.password"
                    aria-describedby="password"
                  />
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-group form-check"> 
                  <input type="checkbox" class="form-check-input" id="is_admin" 
                        v-model="user.is_admin"/>
                  <label for="is_admin">Is Admin</label>
                  <small class="form-text text-muted">Can user access the admin.</small>
                </div>

                <div class="form-group form-check"> 
                  <input type="checkbox" class="form-check-input" id="is_tester" 
                        v-model="user.is_tester"/>
                  <label for="is_tester">Is Test User</label>
                  <small class="form-text text-muted">Can user access closed/test stores.</small>
              </div>
            </div>


            </div>
          </div>
        </div>
        <div class="modal-footer" v-show="user">
          <button type="button" class="btn btn-secondary mr-auto" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary" id="update-user">Update User</button>
        </div>
      </div>
    </div>
  </form>
</template>

<script>
export default {
  data() {
    return {
      user: false,
      errors: []
    };
  },
  methods: {
    loadUser: function(id) {
      return axios
        .get("/admin/users/" + id)
        .then(response => {
          this.user = response.data;
        })
        .catch(function(error) {
          console.log(error);
        });
    },
    updateUser: function(e) {
      e.preventDefault();
      this.errors = [];
      console.log(this.user);

      return axios
        .patch("/admin/users/" + this.user.id, {
          name: this.user.name,
          email: this.user.email,
          password: this.user.password,
          is_admin: this.user.is_admin,
          is_tester: this.user.is_tester,
        })
        .then(function(response) {
          window.location.href = "/admin/users";
        })
        .catch((error) => {
            let err_msg = 'Error Saving User';
            console.log(error);
            if( error.response && error.response.data.message) {
                err_msg += ' - ' + error.response.data.message;
            }
            this.errors.push(err_msg);
            if(error.response && error.response.data.errors) {
                for( const prop in error.response.data.errors) {
                    this.errors.push(error.response.data.errors[prop][0]);
                }
            }
        });
    }
  }
};
</script>
