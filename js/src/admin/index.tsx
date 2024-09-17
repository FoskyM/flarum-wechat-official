import app from 'flarum/admin/app';
import Switch from 'flarum/common/components/Switch';

app.initializers.add('foskym/flarum-wechat-official', () => {
  app.extensionData
    .for('foskym-wechat-official')
    .registerSetting({
      setting: 'foskym-wechat-official.develop_mode',
      label: app.translator.trans('foskym-wechat-official.admin.settings.develop_mode_label'),
      help: app.translator.trans('foskym-wechat-official.admin.settings.develop_mode_description'),
      type: 'boolean',
    })
    .registerSetting(function () {
      let is_develop_mode = this.setting('foskym-wechat-official.develop_mode')();
      if (is_develop_mode === '0') is_develop_mode = false;
      return (
        <div className="Form-group">
          <label>{app.translator.trans(`foskym-wechat-official.admin.settings.app_id_label`)}</label>
          <input type="text" className="FormControl" bidi={this.setting(`foskym-wechat-official.app_id${is_develop_mode ? '_dev' : ''}`)} />
        </div>
      );
    })
    .registerSetting(function () {
      let is_develop_mode = this.setting('foskym-wechat-official.develop_mode')();
      if (is_develop_mode === '0') is_develop_mode = false;
      return (
        <div className="Form-group">
          <label>{app.translator.trans(`foskym-wechat-official.admin.settings.app_secret_label`)}</label>
          <input type="text" className="FormControl" bidi={this.setting(`foskym-wechat-official.app_secret${is_develop_mode ? '_dev' : ''}`)} />
        </div>
      );
    })
    .registerSetting(function () {
      let is_develop_mode = this.setting('foskym-wechat-official.develop_mode')();
      if (is_develop_mode === '0') is_develop_mode = false;
      return (
        <div className="Form-group">
          <label>{app.translator.trans(`foskym-wechat-official.admin.settings.token_label`)}</label>
          <input type="text" className="FormControl" bidi={this.setting(`foskym-wechat-official.token${is_develop_mode ? '_dev' : ''}`)} />
        </div>
      );
    })
    .registerSetting(function () {
      let is_develop_mode = this.setting('foskym-wechat-official.develop_mode')();
      if (is_develop_mode === '0') is_develop_mode = false;
      return (
        <div className="Form-group">
          <label>{app.translator.trans(`foskym-wechat-official.admin.settings.aes_key_label`)}</label>
          <input type="text" className="FormControl" bidi={this.setting(`foskym-wechat-official.aes_key${is_develop_mode ? '_dev' : ''}`)} />
        </div>
      );
    })
    .registerSetting({
      setting: 'foskym-wechat-official.server_url',
      label: app.translator.trans('foskym-wechat-official.admin.settings.server_url_label'),
      help: location.origin + '/api/wechat-official/ping',
      type: 'hidden',
    })
    .registerSetting({
      setting: 'foskym-wechat-official.callback_url',
      label: app.translator.trans('foskym-wechat-official.admin.settings.callback_url_label'),
      help: app.translator.trans('foskym-wechat-official.admin.settings.callback_url_description'),
      type: 'hidden',
    })
    .registerSetting(function () {
      let is_develop_mode = this.setting('foskym-wechat-official.develop_mode')();
      if (is_develop_mode === '0') is_develop_mode = false;
      if (!is_develop_mode) return;
      return (
        <div className="Form-group">
          <label>{app.translator.trans('foskym-wechat-official.admin.settings.template_message_id_label')}</label>
          <div class="helpText">{app.translator.trans('foskym-wechat-official.admin.settings.template_message_id_description')}</div>
          <div class="helpText">{'【标题: {{thing01.DATA}} 内容: {{thing02.DATA}} 时间: {{time01.DATA}}	】'}</div>
          <input type="text" className="FormControl" bidi={this.setting('foskym-wechat-official.template_message_id')} />
        </div>
      );
    })
    .registerSetting({
      setting: 'foskym-wechat-official.enable_push',
      label: app.translator.trans('foskym-wechat-official.admin.settings.enable_push_label'),
      type: 'boolean',
    })
    .registerSetting({
      setting: 'foskym-wechat-official.enable_login_replace',
      label: app.translator.trans('foskym-wechat-official.admin.settings.enable_login_replace_label'),
      help: app.translator.trans('foskym-wechat-official.admin.settings.enable_login_replace_description'),
      type: 'boolean',
    })
    .registerSetting({
      setting: 'foskym-wechat-official.enable_unbind',
      label: app.translator.trans('foskym-wechat-official.admin.settings.enable_unbind_label'),
      type: 'boolean',
    });
});
