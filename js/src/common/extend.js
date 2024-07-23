import Extend from 'flarum/common/extenders';
import WechatLink from './models/WechatLink';
import WechatOriginalData from './models/WechatOriginalData';

export default [
  new Extend.Store()
    .add('wechat_link', WechatLink)
    .add('wechat_original_data', WechatOriginalData)
];
