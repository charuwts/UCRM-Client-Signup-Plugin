import EmberRouter from '@ember/routing/router';
import config from './config/environment';

const Router = EmberRouter.extend({
  location: config.locationType,
  rootURL: config.rootURL
});

Router.map(function() {
  this.route('admin', {path: '/'}, function() {
    this.route('language-settings');
    this.route('service-filters');
    this.route('plugin-config');
  });
});

export default Router;
