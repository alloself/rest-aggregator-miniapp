import{c as n}from"./index-NOwGBND2.js";import{B as i}from"./vue-router-D9vljGhV.js";import{g as r,e as o,h as a,j as d,t,m as s}from"./runtime-dom.esm-bundler-DAH50JVu.js";var l=`
    .p-card {
        background: dt('card.background');
        color: dt('card.color');
        box-shadow: dt('card.shadow');
        border-radius: dt('card.border.radius');
        display: flex;
        flex-direction: column;
    }

    .p-card-caption {
        display: flex;
        flex-direction: column;
        gap: dt('card.caption.gap');
    }

    .p-card-body {
        padding: dt('card.body.padding');
        display: flex;
        flex-direction: column;
        gap: dt('card.body.gap');
    }

    .p-card-title {
        font-size: dt('card.title.font.size');
        font-weight: dt('card.title.font.weight');
    }

    .p-card-subtitle {
        color: dt('card.subtitle.color');
    }
`,c={root:"p-card p-component",header:"p-card-header",body:"p-card-body",caption:"p-card-caption",title:"p-card-title",subtitle:"p-card-subtitle",content:"p-card-content",footer:"p-card-footer"},p=i.extend({name:"card",style:l,classes:c}),m={name:"BaseCard",extends:n,style:p,provide:function(){return{$pcCard:this,$parentInstance:this}}},u={name:"Card",extends:m,inheritAttrs:!1};function f(e,b,y,$,h,v){return o(),r("div",t({class:e.cx("root")},e.ptmi("root")),[e.$slots.header?(o(),r("div",t({key:0,class:e.cx("header")},e.ptm("header")),[s(e.$slots,"header")],16)):a("",!0),d("div",t({class:e.cx("body")},e.ptm("body")),[e.$slots.title||e.$slots.subtitle?(o(),r("div",t({key:0,class:e.cx("caption")},e.ptm("caption")),[e.$slots.title?(o(),r("div",t({key:0,class:e.cx("title")},e.ptm("title")),[s(e.$slots,"title")],16)):a("",!0),e.$slots.subtitle?(o(),r("div",t({key:1,class:e.cx("subtitle")},e.ptm("subtitle")),[s(e.$slots,"subtitle")],16)):a("",!0)],16)):a("",!0),d("div",t({class:e.cx("content")},e.ptm("content")),[s(e.$slots,"content")],16),e.$slots.footer?(o(),r("div",t({key:1,class:e.cx("footer")},e.ptm("footer")),[s(e.$slots,"footer")],16)):a("",!0)],16)],16)}u.render=f;export{u as s};
