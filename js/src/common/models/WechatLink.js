import Model from 'flarum/common/Model';

export default class WechatLink extends Model {
  user_id = Model.attribute('user_id');
  wechat_open_id = Model.attribute('wechat_open_id');
  wechat_original_data = Model.hasOne('wechat_original_data');
}
