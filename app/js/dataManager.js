async function dataManager(params) {
  try {
    return await axios.post(vsl_js_object.ajaxurl, params);
  } catch (err) {
    return err.message;
  }
}
export default dataManager;
