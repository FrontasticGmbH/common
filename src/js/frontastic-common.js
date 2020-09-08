!function(e,t){"object"==typeof exports&&"object"==typeof module?module.exports=t():"function"==typeof define&&define.amd?define([],t):"object"==typeof exports?exports["frontastic-common"]=t():e["frontastic-common"]=t()}(global,(function(){return function(e){var t={};function n(i){if(t[i])return t[i].exports;var r=t[i]={i:i,l:!1,exports:{}};return e[i].call(r.exports,r,r.exports,n),r.l=!0,r.exports}return n.m=e,n.c=t,n.d=function(e,t,i){n.o(e,t)||Object.defineProperty(e,t,{enumerable:!0,get:i})},n.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},n.t=function(e,t){if(1&t&&(e=n(e)),8&t)return e;if(4&t&&"object"==typeof e&&e&&e.__esModule)return e;var i=Object.create(null);if(n.r(i),Object.defineProperty(i,"default",{enumerable:!0,value:e}),2&t&&"string"!=typeof e)for(var r in e)n.d(i,r,function(t){return e[t]}.bind(null,r));return i},n.n=function(e){var t=e&&e.__esModule?function(){return e.default}:function(){return e};return n.d(t,"a",t),t},n.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},n.p="",n(n.s=7)}([function(e){e.exports=JSON.parse('{"schema":[{"name":"Value Order","fields":[{"label":"Strip Label Prefix","field":"stripLabelPrefix","type":"boolean","default":false},{"label":"Sort Order","field":"sortOrder","type":"enum","values":[{"value":"sort-undefined","name":"Undefined"},{"value":"sort-ascending","name":"Ascending"},{"value":"sort-descending","name":"Descending"}],"default":"sort-undefined"}]}]}')},function(e){e.exports=JSON.parse('{"schema":[{"name":"Display Properties","fields":[{"label":"Highlight Menu Item","field":"displayHighlightMenuItem","type":"boolean","default":false},{"label":"Highlight Category Tag","field":"displayHighlightCategoryTag","type":"boolean","default":false},{"label":"Description","field":"displayDescription","type":"string","translatable":true},{"label":"Custom CSS Class","field":"displayClassname","type":"string","translatable":false},{"label":"Image","field":"displayMedia","type":"media","options":{"ratio":"4:1"}}]},{"name":"SEO","fields":[{"label":"Title","field":"seoTitle","type":"string","translatable":true},{"label":"Description","field":"seoDescription","type":"string","translatable":true},{"label":"Keywords","field":"seoKeywords","type":"string","translatable":true}]}]}')},function(e){e.exports=JSON.parse('{"schema":[]}')},function(e,t,n){var i=n(4),r=n(6);e.exports=function(e,t,n){var o=t&&n||0;"string"==typeof e&&(t="binary"===e?new Array(16):null,e=null);var s=(e=e||{}).random||(e.rng||i)();if(s[6]=15&s[6]|64,s[8]=63&s[8]|128,t)for(var a=0;a<16;++a)t[o+a]=s[a];return t||r(s)}},function(e,t,n){var i=n(5);e.exports=function(){return i.randomBytes(16)}},function(e,t){e.exports=require("crypto")},function(e,t){for(var n=[],i=0;i<256;++i)n[i]=(i+256).toString(16).substr(1);e.exports=function(e,t){var i=t||0,r=n;return[r[e[i++]],r[e[i++]],r[e[i++]],r[e[i++]],"-",r[e[i++]],r[e[i++]],"-",r[e[i++]],r[e[i++]],"-",r[e[i++]],r[e[i++]],"-",r[e[i++]],r[e[i++]],r[e[i++]],r[e[i++]],r[e[i++]],r[e[i++]]].join("")}},function(e,t,n){"use strict";n.r(t);const i=["product","product-list","content","content-list"];function r(e,t,n){return void 0!==e?Boolean(e):"stream"===t&&i.includes(n)}function o(e){if(!Array.isArray(e.fields))return{};let t={};for(let n=0;n<e.fields.length;++n){const i=e.fields[n];if(!i.field)continue;const o=i.type||"text";t[i.field]={field:i.field,type:o,sectionName:e.name||"",values:i.values||[],default:c(o,i.default),validate:i.validate||{},fields:i.fields||null,min:void 0===i.min?1:i.min,max:i.max||16,required:r(i.required,o,i.streamType),disabled:!0===i.disabled,translatable:i.translatable}}return t}function s(e,t){let n=e.default;if(void 0!==t[e.field]&&null!==t[e.field]&&(n=t[e.field]),"group"===e.type){let t=n.slice(0,e.max);for(let n=t.length;n<e.min;++n)t[n]={};return i=t,r=e.fields,(i||[]).map(e=>{null!==e&&"object"==typeof e||(e={});for(let t of r)void 0!==e[t.field]&&null!==e[t.field]||(e[t.field]=t.default||null);return e})}var i,r;return n}function a(e,t,n,i){("object"!=typeof i||Array.isArray(i))&&(i={});let r={};for(let o of Object.values(e))r[o.field]=l(o,t,n,i);return r}function l(e,t,n,i){const r=i[e.field];if("group"===e.type){const i=o(e);return s(e,t).map((e,t)=>{const o=void 0!==r&&r.length>t?r[t]:{};return a(i,e,n,o)})}if(void 0!==r)return r;const l=s(e,t);return"stream"===e.type?n[l]||null:l}function c(e,t){if(void 0!==t)return t;switch(e){case"group":return[];case"decimal":case"integer":case"float":case"number":return 0;case"string":case"text":case"markdown":return"";case"json":return"{}";case"boolean":return!1;default:return null}}class u{constructor(e=[],t={}){this.schema=e,this.setConfiguration(t),this.fields={};for(let e=0;e<this.schema.length;++e)this.fields={...this.fields,...o(this.schema[e])}}setConfiguration(e){this.configuration=Array.isArray(e)?{}:e||{}}set(e,t){if(!this.fields[e])throw new Error("Unknown field "+e+" in this configuration schema.");return new u(this.schema,{...this.configuration,[e]:t})}get(e){const t=this.fields[e];return t?s(t,this.configuration):(console.warn("Unknown field "+e+" in this configuration schema."),this.configuration[e]||null)}getField(e){const t=this.fields[e];if(!t)throw new Error("Unknown field "+e+" in this configuration schema.");return t}has(e){return!!this.fields[e]}getSchema(){return this.schema}getConfiguration(){return this.configuration}isFieldRequired(e){return this.getField(e).required}isFieldDisabled(e){return this.getField(e).disabled}hasMissingRequiredValueInField(e,t=!1){const n=this.getField(e),i=this.get(e);return"group"===n.type?i.some(e=>new u([n],e).hasMissingRequiredFieldValues(t)):!!n.required&&(("stream"!==n.type||!t)&&("reference"===n.type?"object"!=typeof i||null===i||"string"!=typeof i.type||""===i.type||"string"!=typeof i.target||""===i.target:null==i||""===i))}hasMissingRequiredFieldValues(e=!1){return Object.keys(this.fields).some(t=>this.hasMissingRequiredValueInField(t,e))}hasMissingRequiredFieldValuesInSection(e,t=!1){return Object.entries(this.fields).some(([n,i])=>i.sectionName===e&&this.hasMissingRequiredValueInField(n,t))}getConfigurationWithResolvedStreams(e={},t={}){return a(this.fields,this.configuration,e,t)}}var d=u,f={NodeConfigurationSchema:n(1),CellConfigurationSchema:n(2)},h=n(3),g=n.n(h),m=function(){return g()()},p=function(e,t,n){if(!e||"object"!=typeof e)return{text:e,locale:t};if(e[t])return{text:e[t],locale:t};if(e[n])return{text:e[n],locale:n};if(!Object.keys(e).length)return{text:"",locale:null,translated:!1};let i=Object.keys(e)[0];return{text:e[i]||"",locale:i,translated:!1}};const y=e=>{switch(e){case"string":case"text":case"markdown":case"json":return!0;default:return!1}},b=e=>void 0!==e.translatable?e.translatable:y(e.type);let v=function(e){return e=(e+"").toString(),encodeURIComponent(e).replace(/!/g,"%21").replace(/'/g,"%27").replace(/\(/g,"%28").replace(/\)/g,"%29").replace(/\*/g,"%2A").replace(/%20/g,"+")};var w=function(e,t,n){let i,r,o=[],s=function(e,t,n){let i,r=[];if(!0===t?t="1":!1===t&&(t="0"),null!=t){if("object"==typeof t){for(i in t)null!=t[i]&&r.push(s(e+"["+i+"]",t[i],n));return r.join(n)}if("function"!=typeof t)return v(e)+"="+v(t);throw new Error("There was an error processing for httpBuildQuery().")}return""};for(r in n||(n="&"),e){i=e[r],t&&!isNaN(r)&&(r=String(t)+r);let a=s(r,i,n);""!==a&&o.push(a)}return o.join(n)};let I=function(e){if("object"!=typeof e)return!1;let t=0;for(let n of Object.keys(e))if(+n!=t++)return!1;return!0},C=function(e){for(let[t,n]of Object.entries(e))n&&"object"==typeof n&&(e[t]=C(n)),I(n)&&(e[t]=Object.values(n));return e};var k=function(e){let t={};return function(e,t){var n,i,r,o,s,a,l,c,u,d,f,h,g,m=String(e).replace(/^&/,"").replace(/&$/,"").split("&"),p=m.length,y=function(e){return decodeURIComponent(e.replace(/\+/g,"%20"))};for(t||(t=this.window),n=0;n<p;n++){for(u=y((c=m[n].split("="))[0]),d=c.length<2?"":y(c[1]);" "===u.charAt(0);)u=u.slice(1);if(u.indexOf("\0")>-1&&(u=u.slice(0,u.indexOf("\0"))),u&&"["!==u.charAt(0)){for(h=[],f=0,i=0;i<u.length;i++)if("["!==u.charAt(i)||f){if("]"===u.charAt(i)&&f&&(h.length||h.push(u.slice(0,f-1)),h.push(u.substr(f,i-f)),f=0,"["!==u.charAt(i+1)))break}else f=i+1;for(h.length||(h=[u]),i=0;i<h[0].length&&(" "!==(l=h[0].charAt(i))&&"."!==l&&"["!==l||(h[0]=h[0].substr(0,i)+"_"+h[0].substr(i+1)),"["!==l);i++);for(a=t,i=0,g=h.length;i<g;i++)if(u=h[i].replace(/^['"]/,"").replace(/['"]$/,""),i!==h.length-1,s=a,""!==u&&" "!==u||0===i)void 0===a[u]&&(a[u]={}),a=a[u];else{for(o in r=-1,a)a.hasOwnProperty(o)&&+o>r&&o.match(/^\d+$/g)&&(r=+o);u=r+1}s[u]=d}}}(e,t),t=C(t),t};const S=Boolean("undefined"==typeof window||!window.location.hostname||"localhost"===window.location.hostname||"[::1]"===window.location.hostname||window.location.hostname.match(/^127(?:\.(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)){3}$/));function T(){if("serviceWorker"in navigator){const e=process.env.PUBLIC_URL||"/";if(new URL(e,window.location).origin!==window.location.origin)return;window.addEventListener("load",()=>{const e="/service-worker.js";S?function(e){fetch(e).then(t=>{404===t.status||-1===t.headers.get("content-type").indexOf("javascript")?navigator.serviceWorker.ready.then(e=>{e.unregister().then(()=>{window.location.reload()})}):x(e)}).catch(()=>{console.log("No internet connection found. App is running in offline mode.")})}(e):x(e)})}}function x(e){navigator.serviceWorker.register(e).then(e=>{e.onupdatefound=()=>{const t=e.installing;t.onstatechange=()=>{"installed"===t.state&&(navigator.serviceWorker.controller?console.log("New content is available; please refresh."):console.log("Content is cached for offline use."))}}}).catch(e=>{console.error("Error during service worker registration:",e)})}var O=function(){let e,t,n={};if(this.registerCallBack=function(e,t){let i=null;do{i=Math.floor(65536*(1+Math.random())).toString(16).substring(1)}while(i in n);return n[i]={hidden:e,active:t},i},this.removeCallBack=function(e){delete n[e]},"undefined"==typeof document?(e=!1,t=!1):void 0!==document.hidden?(e="hidden",t="visibilitychange"):void 0!==document.mozHidden?(e="mozHidden",t="mozvisibilitychange"):void 0!==document.msHidden?(e="msHidden",t="msvisibilitychange"):void 0!==document.webkitHidden&&(e="webkitHidden",t="webkitvisibilitychange"),e&&t){let i=function(){for(let t of Object.values(n))document[e]?t.hidden():t.active()};void 0===document.addEventListener||void 0===document[e]?console.warn("This feature requires a browser, such as Google Chrome or Firefox, that supports the Page Visibility API."):document.addEventListener(t,i,!1)}},j=[{size:12,name:"1",icon:"looks_one"},{size:6,name:"1/2",icon:"looks_two"},{size:4,name:"1/3",icon:"looks_3"},{size:8,name:"2/3",icon:"looks_3"},{size:3,name:"1/4",icon:"looks_4"},{size:9,name:"3/4",icon:"looks_4"},{size:2,name:"1/6",icon:"looks_6"}];var E=class{constructor(e={}){this.tasticId=e.tasticId||m(),this.tasticType=e.tasticType,this.configuration=e.configuration||{};let t=[{name:"General",folded:!0,fields:[{label:"Name",field:"name",type:"string"},{label:"Show on Mobile",field:"mobile",type:"boolean",default:!0},{label:"Show on Tablet",field:"tablet",type:"boolean",default:!0},{label:"Show on Desktop",field:"desktop",type:"boolean",default:!0},{label:"Anchor",field:"tasticId",type:"string",translatable:!1,disabled:!0,default:"#"+this.tasticId}]}];if(e.schema)for(let n=0;n<e.schema.length;++n)t.push(e.schema[n]);this.schema=new d(t,this.configuration)}export(){return{tasticId:this.tasticId,tasticType:this.tasticType,configuration:this.schema.getConfiguration()}}};var D=class{constructor(e={}){if(this.cellId=e.cellId||m(),this.configuration=e.configuration||{},this.customConfiguration=e.customConfiguration||{},this.schema=new d([{name:"Responsive",folded:!0,fields:[{label:"Cell Width",field:"size",type:"enum",values:j&&j.map(e=>({name:e.name,value:e.size})),default:12},{label:"Show on Mobile",field:"mobile",type:"boolean",default:!0},{label:"Show on Tablet",field:"tablet",type:"boolean",default:!0},{label:"Show on Desktop",field:"desktop",type:"boolean",default:!0}]}],this.configuration),this.tastics=[],e.tastics&&e.tastics.length)for(let t=0;t<e.tastics.length;++t)this.tastics.push(new E(e.tastics[t]))}addTastic(e,t={},n=[],i=0){const r=new E({tasticType:e,configuration:t,schema:n});return this.tastics.splice(i,0,r),r}getTastic(e){for(let t of this.tastics)if(t.tasticId===e)return t;throw new Error("Could not find tastic with ID "+e)}getTasticCount(){return this.tastics.length}export(){return{cellId:this.cellId,configuration:this.schema.getConfiguration(),customConfiguration:this.customConfiguration,tastics:this.tastics.map(e=>e.export())}}};var A=class{constructor(e){if(!e.kitDefinitionId)throw new Error("Missing kitDefinitionId in "+JSON.stringify(e));this.kitDefinitionId=e.kitDefinitionId,this.kitId=e.kitId||m(),this.configuration=e.configuration||{}}export(){return{kitId:this.kitId,kitDefinitionId:this.kitDefinitionId,configuration:this.configuration}}};var R=class{constructor(e={}){if(this.regionId=e.regionId||m(),this.configuration=e.configuration||{},this.schema=new d([{name:"Responsive",folded:!0,fields:[{label:"Show on Mobile",field:"mobile",type:"boolean",default:!0},{label:"Show on Tablet",field:"tablet",type:"boolean",default:!0},{label:"Show on Desktop",field:"desktop",type:"boolean",default:!0}]},{name:"Layout",fields:[{label:"Cell Direction",field:"flexDirection",type:"enum",default:"row",values:[{value:"row",name:"Row"},{value:"column",name:"Column"},{value:"row-reverse",name:"Row (reversed)"},{value:"column-reverse",name:"Column (reversed)"}]},{label:"Cell Wrapping",field:"flexWrap",type:"enum",default:"wrap",values:[{value:"nowrap",name:"No Wrapping"},{value:"wrap",name:"Wrap Cells"}]},{label:"Justify Cells",field:"justifyContent",type:"enum",default:"space-between",values:[{value:"flex-start",name:"Put at beginning"},{value:"flex-end",name:"Put at end"},{value:"center",name:"Center Cells"},{value:"space-between",name:"Space between Cells"},{value:"space-around",name:"Space around Cells"},{value:"space-even",name:"Evenly spaced Cells"}]},{label:"Cell Alignment",field:"alignItems",type:"enum",default:"stretch",values:[{value:"flex-start",name:"Align to start"},{value:"flex-end",name:"Align to end"},{value:"center",name:"Center Cells"},{value:"stretch",name:"Stretch Cells"},{value:"baseline",name:"Align to baseline"}]},{label:"Align multiple Cell rows",field:"alignContent",type:"enum",default:"space-between",values:[{value:"flex-start",name:"Put at beginning"},{value:"flex-end",name:"Put at end"},{value:"center",name:"Center rows"},{value:"stretch",name:"Stretch rows"},{value:"space-between",name:"Space between rows"},{value:"space-around",name:"Space around rows"}]}]}],this.configuration),this.elements=[],e.elements&&e.elements.length)for(let t=0;t<e.elements.length;++t)this.addElement(e.elements[t])}addElement(e){if(e.cellId)return this.addCell(e);if(e.kitId)return this.addKit(e);throw new TypeError("Unknown element type: "+JSON.stringify(e))}addCell(e){return this.elements.push(new D(e)),this.elements[this.elements.length-1]}addKit(e){return this.elements.push(new A(e)),this.elements[this.elements.length-1]}getElement(e){const t=Object.keys(e)[0],n=Object.values(e)[0];for(let e of this.elements)if(e[t]===n)return e;throw new Error("Could not find element with ID "+JSON.stringify(n))}getCells(){return this.elements.filter(e=>e instanceof D)}getKits(){return this.elements.filter(e=>e instanceof A)}export(){return{regionId:this.regionId,configuration:this.schema.getConfiguration(),elements:this.elements.map(e=>e.export())}}};var M=class{constructor(e={},t=[],n=[]){this.pageId=e.pageId||null,this.nodes=e.nodes||[],this.layoutId=e.layoutId||"three_rows",this.name=e.name||"Unnamed Page",this.regions={},this.tasticSchemas=(n||[]).map(e=>e.configurationSchema);for(let n=0;n<t.length;++n){let i=t[n];e.regions&&e.regions[i]&&e.regions[i].elements&&e.regions[i].elements.length&&(e.regions[i].elements=this.mapTastics(e.regions[i].elements)),this.createRegion(i,e.regions&&e.regions[i]||{})}}mapTastics(e){for(let t=0;t<e.length;++t){let n=e[t];if(n.cellId&&(n.tastics&&n.tastics.length))for(let e=0;e<n.tastics.length;++e){let t=n.tastics[e];t.schema={schema:[]};for(let e of this.tasticSchemas)if(e.tasticType===t.tasticType){t.schema=e.schema;break}}}return e}createRegion(e,t){t.regionId=e,this.regions[e]=new R(t)}getRegion(e){if(!this.regions[e])throw new Error("Region with identifier "+e+" unknown.");return this.regions[e]}addCell(e,t={}){return this.getRegion(e).addCell({configuration:t})}duplicateCell(e,t){const n=this.addCell(e,t.configuration);return t.tastics.forEach((t,i)=>{this.addTastic(e,n.cellId,t.tasticType,i,t.configuration)}),n}addKit(e,t){return this.getRegion(e).addKit(t)}findElement(e){const t=Object.keys(e)[0],n=Object.values(e)[0];for(let e in this.regions)for(let[i,r]of Object.entries(this.regions[e].elements))if(r[t]===n)return[e,+i];throw new Error("Could not find element with "+JSON.stringify(e))}hasElement(e){try{return!!this.findElement(e)}catch(e){return!1}}getElement(e){let[t,n]=this.findElement(e);return this.regions[t].elements[n]}removeElement(e){let[t,n]=this.findElement(e);this.regions[t].elements.splice(n,1)}moveElement(e,t){if(!this.regions[t.region])throw new Error("Unknown target region "+t.region);let[n,i]=this.findElement(e),r=this.regions[n].elements.splice(i,1)[0];this.regions[t.region].elements.splice(void 0===t.element?this.regions[t.region].elements.length:t.element-(n===t.region&&t.element>i?1:0),0,r)}addTastic(e,t,n,i,r={}){let o=null;for(let e of this.tasticSchemas)if(e.tasticType===n){o=e;break}return this.getRegion(e).getElement({cellId:t}).addTastic(n,r,o,i)}getTastics(){let e=[];return Object.values(this.regions).forEach(t=>{t.getCells().forEach(t=>{e=e.concat(t.tastics)})}),e}findTastic(e){for(let t in this.regions)for(let n=0;n<this.regions[t].elements.length;++n)for(let i in this.regions[t].elements[n].tastics||[]){if(this.regions[t].elements[n].tastics[i].tasticId===e)return[t,+n,+i]}throw new Error("Could not find tastic with id "+e)}hasTastic(e){try{return!!this.findTastic(e)}catch(e){return!1}}getTastic(e){let[t,n,i]=this.findTastic(e);return this.regions[t].elements[n].tastics[i]}removeTastic(e){let[t,n,i]=this.findTastic(e);this.regions[t].elements[n].tastics.splice(i,1)}moveTastic(e,t){let[n,i,r]=this.findTastic(e),o=this.regions[n].elements[i].tastics.splice(r,1)[0],[s,a]=this.findElement({cellId:t.cell});this.regions[s].elements[a].tastics.splice(void 0===t.tasticDropPosition?this.regions[s].elements[a].tastics.length:t.tasticDropPosition-(n===s&&i===a&&t.tasticDropPosition>=r?1:0),0,o)}duplicateTastic(e,t){const[n,,i]=this.findTastic(e),r=this.getTastic(e);return this.addTastic(n,t,r.tasticType,i+1,r.configuration)}export(){let e={};for(let[t,n]of Object.entries(this.regions))e[t]=n.export();return{pageId:this.pageId,nodes:this.nodes,layoutId:this.layoutId,name:this.name,regions:e}}};const P=(e,t,n)=>{n={resourceType:"image",type:"upload",...n};let i=[];for(let[e,t]of Object.entries(n))switch(e){case"secure":case"resourceType":case"type":break;case"background":case"crop":case"fetch_format":case"gravity":case"height":case"quality":case"width":case"x":case"y":t&&i.push(e[0]+"_"+t);break;default:throw new Error("Unhandled image transformation "+e)}return i.sort(),`https://res.cloudinary.com/${t.cloudName}/${n.resourceType}/${n.type}/${i.join(",")}/${r=e,encodeURI(r).replace(/[?=]/g,(function(e){return"%"+e.charCodeAt(0).toString(16).toUpperCase()}))}`;var r};var F=class{constructor(e){this.configuration={cloudName:e.cloudName}}getImageUrl(e,t,n,i={}){return P(e.mediaId,this.configuration,{fetch_format:e.format&&"svg"===e.format?void 0:"auto",width:t,height:n,quality:"auto",secure:!0,...this.getGravityOptions(i),...this.cropOptions(i)})}getFetchImageUrl(e,t,n,i={}){return e.startsWith("//")&&(e="https:"+e),P(e,this.configuration,{fetch_format:"auto",type:"fetch",width:t,height:n,quality:"auto",secure:!0,...this.getGravityOptions(i),...this.cropOptions(i)})}getImageUrlWithoutDefaults(e,t,n,i={}){return P(e.mediaId,this.configuration,{width:t,height:n,...i})}getGravityOptions(e){if(e.crop)return{};let t={gravity:"faces:auto"};return e.gravity&&(t.gravity="custom"===e.gravity.mode?"xy_center":e.gravity.mode,e.gravity.coordinates&&(t.x=e.gravity.coordinates.x,t.y=e.gravity.coordinates.y)),t}cropOptions(e){let t={crop:"fill"};return e.crop&&(t.crop=e.crop),e.background&&(t.background=e.background),t}};var _=class{constructor(){this.imageSizes=[16,32,64,128,256,512,1024,2048]}getImageDimensions(e,t,n,i=null,r=1){let o=this.getFloatRatio(e,i),s=Math.ceil(+t*r);return[s,n&&!i?Math.ceil(+n*r):Math.ceil(s*o)]}getFloatRatio(e,t=null){if(!t&&e&&e.width&&e.height)return e.height/e.width;const n=String(t).match(/([0-9]+):([0-9]+)/);return n?n[2]/n[1]:t}getImageLink(e,t,n,i,r,o={},s=1){let a=this.getMediaApi(t),[l,c]=this.getImageDimensions(e,n,i,r,s),u=l/c;if(["fill","pad"].includes(o.crop)){for(let e=0;e<this.imageSizes.length;++e)if(this.imageSizes[e]>=l){l=this.imageSizes[e];break}c=o.autoHeight?null:Math.ceil(l/u)}return"string"==typeof e?a.getFetchImageUrl(e,l,c,o):a.getImageUrl(e,l,c,o)}getMediaApi(e){switch(e.media.engine){case"cloudinary":return new F(e.media);default:throw new Error("No valid media API found.")}}static getElementDimensions(e){let t=0;if(getComputedStyle){let n=getComputedStyle(e);t+=parseFloat(n.paddingLeft)+parseFloat(n.paddingRight)}return{width:e.clientWidth-t,height:e.clientHeight}}},N=n(0),U={enum:N,localizedEnum:N},z=(e,t)=>Object.fromEntries(Object.entries(e).filter(([e])=>!t.includes(e))),q=(e,t,n=!1)=>{let i=!1;return function(){let r=this,o=arguments,s=function(){i=null,n||e.apply(r,o)},a=n&&!i;clearTimeout(i),i=setTimeout(s,t),a&&e.apply(r,o)}},W=(e,t)=>{let n=!1;return function(){n||(e.apply(this,arguments),n=!0,setTimeout((function(){n=!1}),t))}};n.d(t,"ConfigurationSchema",(function(){return d})),n.d(t,"DefaultSchemas",(function(){return f})),n.d(t,"generateId",(function(){return m})),n.d(t,"getTranslation",(function(){return p})),n.d(t,"httpBuildQuery",(function(){return w})),n.d(t,"httpParseQuery",(function(){return k})),n.d(t,"isTranslatableByDefault",(function(){return y})),n.d(t,"shouldFieldBeTranslated",(function(){return b})),n.d(t,"registerServiceWorker",(function(){return T})),n.d(t,"VisibilityChange",(function(){return O})),n.d(t,"cellDimensions",(function(){return j})),n.d(t,"Cell",(function(){return D})),n.d(t,"Page",(function(){return M})),n.d(t,"Region",(function(){return R})),n.d(t,"Tastic",(function(){return E})),n.d(t,"MediaApi",(function(){return _})),n.d(t,"FacetTypeSchemaMap",(function(){return U})),n.d(t,"omit",(function(){return z})),n.d(t,"debounce",(function(){return q})),n.d(t,"throttle",(function(){return W}))}])}));