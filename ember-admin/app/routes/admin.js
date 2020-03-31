import Route from '@ember/routing/route';
import ENV from "../config/environment";

export default Route.extend({
  afterModel() {
    if (ENV.APP.initialRoute) {
      if (ENV.APP.initialRoute !== 'false') {
        this.transitionTo('admin.'+ENV.APP.initialRoute);
      }
    }
  }

});
