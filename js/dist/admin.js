(()=>{var e=e=>{"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},t={};(()=>{"use strict";e(t),flarum.core.compat.extend,app.initializers.add("gtdxyz-money-plus",(function(){app.extensionData.for("gtdxyz-money-plus").registerSetting({setting:"gtdxyz-money-plus.moneyname",label:app.translator.trans("gtdxyz-money-plus.admin.settings.moneyname"),type:"text"}).registerSetting((function(){return m("div",{className:"Form-group"},m("label",null,app.translator.trans("gtdxyz-money-plus.admin.settings.moneyforpost")),m("input",{type:"number",className:"FormControl",step:"any",bidi:this.setting("gtdxyz-money-plus.moneyforpost")}))})).registerSetting({setting:"gtdxyz-money-plus.postminimumlength",label:app.translator.trans("gtdxyz-money-plus.admin.settings.postminimumlength"),type:"number"}).registerSetting((function(){return m("div",{className:"Form-group"},m("label",null,app.translator.trans("gtdxyz-money-plus.admin.settings.moneyfordiscussion")),m("input",{type:"number",className:"FormControl",step:"any",bidi:this.setting("gtdxyz-money-plus.moneyfordiscussion")}))})).registerSetting((function(){return m("div",{className:"Form-group"},m("label",null,app.translator.trans("gtdxyz-money-plus.admin.settings.moneyforlike")),m("div",{class:"helpText"},app.translator.trans("gtdxyz-money-plus.admin.settings.helpextensionlikes")),m("input",{type:"number",className:"FormControl",step:"any",bidi:this.setting("gtdxyz-money-plus.moneyforlike")}))})).registerSetting({setting:"gtdxyz-money-plus.autoremove.hidden",label:app.translator.trans("gtdxyz-money-plus.admin.autoremove.1"),type:"checkbox"}).registerSetting({setting:"gtdxyz-money-plus.autoremove.deleted",label:app.translator.trans("gtdxyz-money-plus.admin.autoremove.2"),type:"checkbox"}).registerSetting({setting:"gtdxyz-money-plus.noshowzero",label:app.translator.trans("gtdxyz-money-plus.admin.settings.noshowzero"),type:"checkbox"}).registerPermission({icon:"fas fa-money-bill",label:app.translator.trans("gtdxyz-money-plus.admin.permissions.edit_money_label"),permission:"user.edit_money"},"moderate")}))})(),module.exports=t})();
//# sourceMappingURL=admin.js.map