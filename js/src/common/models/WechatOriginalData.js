import Model from 'flarum/common/Model';

export default class WechatOriginalData extends Model {
  openid = Model.attribute('openid');
  nickname = Model.attribute('nickname');
  sex = Model.attribute('sex');
  language = Model.attribute('language');
  city = Model.attribute('city');
  province = Model.attribute('province');
  country = Model.attribute('country');
  headimgurl = Model.attribute('headimgurl');
  privilege = Model.attribute('privilege');
}
