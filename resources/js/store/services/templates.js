import upperFirst from 'lodash/upperFirst'
import camelCase from 'lodash/camelCase'

export default {
  loadComponents(){
    // per-store override component template loading
    const requireComponent = require.context(
      // The relative path of the components folder
      '../components',
      // Whether or not to look in subfolders
      true,
      // The regular expression used to match base component filenames
      /[A-Z]\w+\.(vue|js)$/
    );

    requireComponent.keys().forEach(fileName => {
      // Get component config
      const componentConfig = requireComponent(fileName);

      // Get PascalCase name of component
      var componentName = upperFirst(
        camelCase(
          // Gets the file name regardless of folder depth
          fileName
            .split('/')
            .pop()
            .replace(/\.\w+$/, '')
        )
      );

      // add store ID if needed
      const fileNameData = fileName.split('/');
      if(fileNameData[1] == 'stores'){
        componentName += '_' + fileNameData[2];
      }

      // Register component globally
      Vue.component(
        componentName,
        componentConfig.default || componentConfig
      );
    });
  },
  getComponentName(name, storeSlug){
    var components = [];
    var storeComponentName = name + '_' + storeSlug;
    for (var key in Vue.options.components) {
      components.push(key);
    }
    console.log(components, storeComponentName);
    if (components.indexOf(storeComponentName) !== -1) {
      return storeComponentName;
    }
    else{
      return name
    }
  }
}
