(()=>{var o={n:t=>{var e=t&&t.__esModule?()=>t.default:()=>t;return o.d(e,{a:e}),e},d:(t,e)=>{for(var n in e)o.o(e,n)&&!o.o(t,n)&&Object.defineProperty(t,n,{enumerable:!0,get:e[n]})},o:(o,t)=>Object.prototype.hasOwnProperty.call(o,t)};(()=>{"use strict";const t=flarum.core.compat["common/app"];o.n(t)().initializers.add("foskym/flarum-wechat-official",(function(){console.log("[foskym/flarum-wechat-official] Hello, forum and admin!")}));const e=flarum.core.compat["forum/app"];var n=o.n(e);const a=flarum.core.compat["common/extend"],c=flarum.core.compat["forum/components/NotificationGrid"];var r=o.n(c);const i=flarum.core.compat["forum/components/SettingsPage"];var s=o.n(i);const f=flarum.core.compat["common/components/Alert"];var u=o.n(f);const l=flarum.core.compat["common/components/Button"];var p=o.n(l);const h=flarum.core.compat["common/components/LinkButton"];var d=o.n(h);flarum.core.compat["common/components/Link"],flarum.core.compat["common/components/Page"],flarum.core.compat["common/helpers/icon"];const w=flarum.core.compat["common/models/User"];var k=o.n(w);const y=flarum.core.compat["common/Model"];var b=o.n(y);const g=flarum.core.compat["forum/components/LogInModal"];var _=o.n(g);function v(){var o=n().forum.attribute("foskym-wechat-official.app_id"),t=n().forum.attribute("baseUrl")+"/wechat-official/callback";return"https://open.weixin.qq.com/connect/oauth2/authorize?appid="+o+"&redirect_uri="+encodeURIComponent(t)+"&response_type=code&scope=snsapi_userinfo&state=wechat#wechat_redirect"}function x(){return/MicroMessenger/i.test(navigator.userAgent)}function L(){return/wechat_redirect=true/.test(location.href)}flarum.core.compat["forum/components/IndexPage"],n().initializers.add("foskym/flarum-wechat-official",(function(){k().prototype.WechatAuth=b().attribute("WechatAuth"),(0,a.extend)(r().prototype,"notificationMethods",(function(o){n().forum.attribute("foskym-wechat-official.enable_push")&&o.add("push",{name:"push",icon:"fab fa-weixin",label:n().translator.trans("foskym-wechat-official.forum.settings.push_notification_label")})})),(0,a.extend)(s().prototype,"accountItems",(function(o){var t=this,e=v();o.add("wechat",this.user.WechatAuth().isLinked?m(p(),{icon:"fab fa-weixin",className:"Button Button--Wechat",onclick:function(){n().request({method:"POST",url:n().forum.attribute("apiUrl")+"/wechat-official/unlink"}).then((function(){t.user.WechatAuth().isLinked=!1,n().alerts.show(u(),{type:"success",children:n().translator.trans("foskym-wechat-official.forum.settings.wechat_unlink_success")}),m.redraw()}))}},n().translator.trans("foskym-wechat-official.forum.settings.wechat_unlink")):m(d(),{icon:"fab fa-weixin",className:"Button Button--Wechat",href:e,external:!0},this.user.WechatAuth().isLinked?n().translator.trans("foskym-wechat-official.forum.settings.wechat_linked"):n().translator.trans("foskym-wechat-official.forum.settings.bind_wechat")),-100)})),(0,a.extend)(_().prototype,"oncreate",(function(){if(x()&&!L()&&n().forum.attribute("foskym-wechat-official.enable_login_replace")){var o=v();window.location.href=o}else $(".LogInModal").show()})),document.addEventListener("DOMContentLoaded",(function(){x()&&L()&&(n().session.user?m.route.set("/settings"):setTimeout((function(){n().modal.show(_()),$(".LogInModal").show()}),200))}))}))})(),module.exports={}})();
//# sourceMappingURL=forum.js.map