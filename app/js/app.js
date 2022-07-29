// import store from "./store.js";
// import router from "./router.js";
import StoreViewer from "./components/StoreViewer.js";
import dataManager from "./dataManager.js";


// App Initialization
var app = new Vue({
  el: "#vslApp",
  // store: store,
  components: { StoreViewer },
  data: {
    stores: [],
    loading: false,
  },
  methods: {
    
  },
  computed: {
     
  },
  created() {
    console.log("App Created");
  },
  mounted() {
    console.log("App Mounted");
    this.loading = true;
    dataManager(
      Qs.stringify({
        action: "vsl_stores_list",
        nonce: vsl_js_object.nonce,
        modules: "xxx",
      })
    ).then((res) => {
      this.stores = res.data;
      this.loading = false;
    });
  }
  // router,
});
