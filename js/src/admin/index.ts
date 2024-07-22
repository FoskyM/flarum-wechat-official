import app from 'flarum/admin/app';

app.initializers.add('foskym/flarum-wechat-official', () => {
  app.extensionData
    .for('foskym-wechat-official')
    .registerSetting({
      setting: 'foskym-wechat-official.app_id',
      label: app.translator.trans('foskym-wechat-official.admin.settings.app_id_label'),
      type: 'text'
    })
    .registerSetting({
      setting: 'foskym-wechat-official.app_secret',
      label: app.translator.trans('foskym-wechat-official.admin.settings.app_secret_label'),
      type: 'text'
    })
    .registerSetting({
      setting: 'foskym-wechat-official.callback_url',
      label: app.translator.trans('foskym-wechat-official.admin.settings.callback_url_label'),
      help: app.translator.trans('foskym-wechat-official.admin.settings.callback_url_description'),
      type: 'hidden'
    })
    .registerSetting({
      setting: 'foskym-wechat-official.template_message_id',
      label: app.translator.trans('foskym-wechat-official.admin.settings.template_message_id_label'),
      type: 'text',
      help: app.translator.trans('foskym-wechat-official.admin.settings.template_message_id_description') + ' 【标题: {{thing01.DATA}} 内容: {{thing02.DATA}} 时间: {{time01.DATA}}	】'
    })
    .registerSetting({
      setting: 'foskym-wechat-official.enable_push',
      label: app.translator.trans('foskym-wechat-official.admin.settings.enable_push_label'),
      type: 'boolean'
    })
});
