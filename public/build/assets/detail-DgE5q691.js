import{s as ge}from"./SmartForm.vue_vue_type_style_index_0_scoped_f0658e56_lang-CdUrnwZc.js";import{i as it,c as H,g as at,R as st,b as _,e as jt,f as Lt,s as dt}from"./index-scBVuG3i.js";import{g as Ee,h as je,i as Le,j as Rt,b as Dt,k as At,d as Ut,e as $t,f as Vt,a as q,l as Oe,s as ut,c as Nt}from"./index-DPm8dB91.js";import{B as ye,a7 as Ht,Q as L,ab as ct,aa as he,C as Z,ad as Wt,K as Re,Z as Gt,ae as G,af as De,a8 as Ae,ag as Ue,ah as $e,ai as xe,aj as Ie,ak as Yt,f as ke,z as te,al as Ve,l as pt,$ as ft,e as me,O as Ne,W as Xt}from"./vue-router-B2AH15Lj.js";import{g as de,_ as qt}from"./index-CqymIJAl.js";import{t as f,g as m,e as a,b as g,h as w,s as x,A as j,v as E,j as v,Z as Ke,y as Zt,z as Qt,F as O,l as M,w as y,a4 as be,q as U,m as k,Y as He,d as Y,a9 as Q,B as ee,c as ve,r as R,a1 as Jt,f as S,u as K,o as _t,$ as We}from"./runtime-dom.esm-bundler-zEsCHIXO.js";import{a as ht}from"./main-DAA4oTlI.js";import{C as en,F as Ge}from"./const-R-p_VP0G.js";import{s as tn}from"./index-DzkRoIvQ.js";import{u as nn,S as ln,a as on,s as mt}from"./SmartForm-B6xVd0oS.js";import{s as rn}from"./index-CRILyC6I.js";var an=`
    .p-treetable {
        position: relative;
    }

    .p-treetable-table {
        border-spacing: 0;
        border-collapse: separate;
        width: 100%;
    }

    .p-treetable-scrollable > .p-treetable-table-container {
        position: relative;
    }

    .p-treetable-scrollable-table > .p-treetable-thead {
        inset-block-start: 0;
        z-index: 1;
    }

    .p-treetable-scrollable-table > .p-treetable-frozen-tbody {
        position: sticky;
        z-index: 1;
    }

    .p-treetable-scrollable-table > .p-treetable-tfoot {
        inset-block-end: 0;
        z-index: 1;
    }

    .p-treetable-scrollable .p-treetable-frozen-column {
        position: sticky;
        background: dt('treetable.header.cell.background');
    }

    .p-treetable-scrollable th.p-treetable-frozen-column {
        z-index: 1;
    }

    .p-treetable-scrollable > .p-treetable-table-container > .p-treetable-table > .p-treetable-thead {
        background: dt('treetable.header.cell.background');
    }

    .p-treetable-scrollable > .p-treetable-table-container > .p-treetable-table > .p-treetable-tfoot {
        background: dt('treetable.footer.cell.background');
    }

    .p-treetable-flex-scrollable {
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .p-treetable-flex-scrollable > .p-treetable-table-container {
        display: flex;
        flex-direction: column;
        flex: 1;
        height: 100%;
    }

    .p-treetable-scrollable-table > .p-treetable-tbody > .p-treetable-row-group-header {
        position: sticky;
        z-index: 1;
    }

    .p-treetable-resizable-table > .p-treetable-thead > tr > th,
    .p-treetable-resizable-table > .p-treetable-tfoot > tr > td,
    .p-treetable-resizable-table > .p-treetable-tbody > tr > td {
        overflow: hidden;
        white-space: nowrap;
    }

    .p-treetable-resizable-table > .p-treetable-thead > tr > th.p-treetable-resizable-column:not(.p-treetable-frozen-column) {
        background-clip: padding-box;
        position: relative;
    }

    .p-treetable-resizable-table-fit > .p-treetable-thead > tr > th.p-treetable-resizable-column:last-child .p-treetable-column-resizer {
        display: none;
    }

    .p-treetable-column-resizer {
        display: block;
        position: absolute;
        inset-block-start: 0;
        inset-inline-end: 0;
        margin: 0;
        width: dt('treetable.column.resizer.width');
        height: 100%;
        padding: 0;
        cursor: col-resize;
        border: 1px solid transparent;
    }

    .p-treetable-column-header-content {
        display: flex;
        align-items: center;
        gap: dt('treetable.header.cell.gap');
    }

    .p-treetable-column-resize-indicator {
        width: dt('treetable.resize.indicator.width');
        position: absolute;
        z-index: 10;
        display: none;
        background: dt('treetable.resize.indicator.color');
    }

    .p-treetable-mask {
        position: absolute;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 2;
    }

    .p-treetable-paginator-top {
        border-color: dt('treetable.paginator.top.border.color');
        border-style: solid;
        border-width: dt('treetable.paginator.top.border.width');
    }

    .p-treetable-paginator-bottom {
        border-color: dt('treetable.paginator.bottom.border.color');
        border-style: solid;
        border-width: dt('treetable.paginator.bottom.border.width');
    }

    .p-treetable-header {
        background: dt('treetable.header.background');
        color: dt('treetable.header.color');
        border-color: dt('treetable.header.border.color');
        border-style: solid;
        border-width: dt('treetable.header.border.width');
        padding: dt('treetable.header.padding');
    }

    .p-treetable-footer {
        background: dt('treetable.footer.background');
        color: dt('treetable.footer.color');
        border-color: dt('treetable.footer.border.color');
        border-style: solid;
        border-width: dt('treetable.footer.border.width');
        padding: dt('treetable.footer.padding');
    }

    .p-treetable-header-cell {
        padding: dt('treetable.header.cell.padding');
        background: dt('treetable.header.cell.background');
        border-color: dt('treetable.header.cell.border.color');
        border-style: solid;
        border-width: 0 0 1px 0;
        color: dt('treetable.header.cell.color');
        font-weight: normal;
        text-align: start;
        transition:
            background dt('treetable.transition.duration'),
            color dt('treetable.transition.duration'),
            border-color dt('treetable.transition.duration'),
            outline-color dt('treetable.transition.duration'),
            box-shadow dt('treetable.transition.duration');
    }

    .p-treetable-column-title {
        font-weight: dt('treetable.column.title.font.weight');
    }

    .p-treetable-tbody > tr {
        outline-color: transparent;
        background: dt('treetable.row.background');
        color: dt('treetable.row.color');
        transition:
            background dt('treetable.transition.duration'),
            color dt('treetable.transition.duration'),
            border-color dt('treetable.transition.duration'),
            outline-color dt('treetable.transition.duration'),
            box-shadow dt('treetable.transition.duration');
    }

    .p-treetable-tbody > tr > td {
        text-align: start;
        border-color: dt('treetable.body.cell.border.color');
        border-style: solid;
        border-width: 0 0 1px 0;
        padding: dt('treetable.body.cell.padding');
    }

    .p-treetable-hoverable .p-treetable-tbody > tr:not(.p-treetable-row-selected):hover {
        background: dt('treetable.row.hover.background');
        color: dt('treetable.row.hover.color');
    }

    .p-treetable-tbody > tr.p-treetable-row-selected {
        background: dt('treetable.row.selected.background');
        color: dt('treetable.row.selected.color');
    }

    .p-treetable-tbody > tr:has(+ .p-treetable-row-selected) > td {
        border-block-end-color: dt('treetable.body.cell.selected.border.color');
    }

    .p-treetable-tbody > tr.p-treetable-row-selected > td {
        border-block-end-color: dt('treetable.body.cell.selected.border.color');
    }

    .p-treetable-tbody > tr:focus-visible,
    .p-treetable-tbody > tr.p-treetable-contextmenu-row-selected {
        box-shadow: dt('treetable.row.focus.ring.shadow');
        outline: dt('treetable.row.focus.ring.width') dt('treetable.row.focus.ring.style') dt('treetable.row.focus.ring.color');
        outline-offset: dt('treetable.row.focus.ring.offset');
    }

    .p-treetable-tfoot > tr > td {
        text-align: start;
        padding: dt('treetable.footer.cell.padding');
        border-color: dt('treetable.footer.cell.border.color');
        border-style: solid;
        border-width: 0 0 1px 0;
        color: dt('treetable.footer.cell.color');
        background: dt('treetable.footer.cell.background');
    }

    .p-treetable-column-footer {
        font-weight: dt('treetable.column.footer.font.weight');
    }

    .p-treetable-sortable-column {
        cursor: pointer;
        user-select: none;
        outline-color: transparent;
    }

    .p-treetable-column-title,
    .p-treetable-sort-icon,
    .p-treetable-sort-badge {
        vertical-align: middle;
    }

    .p-treetable-sort-icon {
        color: dt('treetable.sort.icon.color');
        font-size: dt('treetable.sort.icon.size');
        width: dt('treetable.sort.icon.size');
        height: dt('treetable.sort.icon.size');
        transition: color dt('treetable.transition.duration');
    }

    .p-treetable-sortable-column:not(.p-treetable-column-sorted):hover {
        background: dt('treetable.header.cell.hover.background');
        color: dt('treetable.header.cell.hover.color');
    }

    .p-treetable-sortable-column:not(.p-treetable-column-sorted):hover .p-treetable-sort-icon {
        color: dt('treetable.sort.icon.hover.color');
    }

    .p-treetable-column-sorted {
        background: dt('treetable.header.cell.selected.background');
        color: dt('treetable.header.cell.selected.color');
    }

    .p-treetable-column-sorted .p-treetable-sort-icon {
        color: dt('treetable.header.cell.selected.color');
    }

    .p-treetable-sortable-column:focus-visible {
        box-shadow: dt('treetable.header.cell.focus.ring.shadow');
        outline: dt('treetable.header.cell.focus.ring.width') dt('treetable.header.cell.focus.ring.style') dt('treetable.header.cell.focus.ring.color');
        outline-offset: dt('treetable.header.cell.focus.ring.offset');
    }

    .p-treetable-hoverable .p-treetable-selectable-row {
        cursor: pointer;
    }

    .p-treetable-loading-icon {
        font-size: dt('treetable.loading.icon.size');
        width: dt('treetable.loading.icon.size');
        height: dt('treetable.loading.icon.size');
    }

    .p-treetable-gridlines .p-treetable-header {
        border-width: 1px 1px 0 1px;
    }

    .p-treetable-gridlines .p-treetable-footer {
        border-width: 0 1px 1px 1px;
    }

    .p-treetable-gridlines .p-treetable-paginator-top {
        border-width: 1px 1px 0 1px;
    }

    .p-treetable-gridlines .p-treetable-paginator-bottom {
        border-width: 0 1px 1px 1px;
    }

    .p-treetable-gridlines .p-treetable-thead > tr > th {
        border-width: 1px 0 1px 1px;
    }

    .p-treetable-gridlines .p-treetable-thead > tr > th:last-child {
        border-width: 1px;
    }

    .p-treetable-gridlines .p-treetable-tbody > tr > td {
        border-width: 1px 0 0 1px;
    }

    .p-treetable-gridlines .p-treetable-tbody > tr > td:last-child {
        border-width: 1px 1px 0 1px;
    }

    .p-treetable-gridlines .p-treetable-tbody > tr:last-child > td {
        border-width: 1px 0 1px 1px;
    }

    .p-treetable-gridlines .p-treetable-tbody > tr:last-child > td:last-child {
        border-width: 1px;
    }

    .p-treetable-gridlines .p-treetable-tfoot > tr > td {
        border-width: 1px 0 1px 1px;
    }

    .p-treetable-gridlines .p-treetable-tfoot > tr > td:last-child {
        border-width: 1px 1px 1px 1px;
    }

    .p-treetable.p-treetable-gridlines .p-treetable-thead + .p-treetable-tfoot > tr > td {
        border-width: 0 0 1px 1px;
    }

    .p-treetable.p-treetable-gridlines .p-treetable-thead + .p-treetable-tfoot > tr > td:last-child {
        border-width: 0 1px 1px 1px;
    }

    .p-treetable.p-treetable-gridlines:has(.p-treetable-thead):has(.p-treetable-tbody) .p-treetable-tbody > tr > td {
        border-width: 0 0 1px 1px;
    }

    .p-treetable.p-treetable-gridlines:has(.p-treetable-thead):has(.p-treetable-tbody) .p-treetable-tbody > tr > td:last-child {
        border-width: 0 1px 1px 1px;
    }

    .p-treetable.p-treetable-gridlines:has(.p-treetable-tbody):has(.p-treetable-tfoot) .p-treetable-tbody > tr:last-child > td {
        border-width: 0 0 0 1px;
    }

    .p-treetable.p-treetable-gridlines:has(.p-treetable-tbody):has(.p-treetable-tfoot) .p-treetable-tbody > tr:last-child > td:last-child {
        border-width: 0 1px 0 1px;
    }

    .p-treetable.p-treetable-sm .p-treetable-header {
        padding: 0.375rem 0.5rem;
    }

    .p-treetable.p-treetable-sm .p-treetable-thead > tr > th {
        padding: 0.375rem 0.5rem;
    }

    .p-treetable.p-treetable-sm .p-treetable-tbody > tr > td {
        padding: 0.375rem 0.5rem;
    }

    .p-treetable.p-treetable-sm .p-treetable-tfoot > tr > td {
        padding: 0.375rem 0.5rem;
    }

    .p-treetable.p-treetable-sm .p-treetable-footer {
        padding: 0.375rem 0.5rem;
    }

    .p-treetable.p-treetable-lg .p-treetable-header {
        padding: 0.9375rem 1.25rem;
    }

    .p-treetable.p-treetable-lg .p-treetable-thead > tr > th {
        padding: 0.9375rem 1.25rem;
    }

    .p-treetable.p-treetable-lg .p-treetable-tbody > tr > td {
        padding: 0.9375rem 1.25rem;
    }

    .p-treetable.p-treetable-lg .p-treetable-tfoot > tr > td {
        padding: 0.9375rem 1.25rem;
    }

    .p-treetable.p-treetable-lg .p-treetable-footer {
        padding: 0.9375rem 1.25rem;
    }

    .p-treetable-body-cell-content {
        display: flex;
        align-items: center;
        gap: dt('treetable.body.cell.gap');
    }

    .p-treetable-tbody > tr.p-treetable-row-selected .p-treetable-node-toggle-button {
        color: inherit;
    }

    .p-treetable-node-toggle-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        position: relative;
        width: dt('treetable.node.toggle.button.size');
        height: dt('treetable.node.toggle.button.size');
        color: dt('treetable.node.toggle.button.color');
        border: 0 none;
        background: transparent;
        cursor: pointer;
        border-radius: dt('treetable.node.toggle.button.border.radius');
        transition:
            background dt('treetable.transition.duration'),
            color dt('treetable.transition.duration'),
            border-color dt('treetable.transition.duration'),
            outline-color dt('treetable.transition.duration'),
            box-shadow dt('treetable.transition.duration');
        outline-color: transparent;
        user-select: none;
    }

    .p-treetable-node-toggle-button:enabled:hover {
        color: dt('treetable.node.toggle.button.hover.color');
        background: dt('treetable.node.toggle.button.hover.background');
    }

    .p-treetable-tbody > tr.p-treetable-row-selected .p-treetable-node-toggle-button:hover {
        background: dt('treetable.node.toggle.button.selected.hover.background');
        color: dt('treetable.node.toggle.button.selected.hover.color');
    }

    .p-treetable-node-toggle-button:focus-visible {
        box-shadow: dt('treetable.node.toggle.button.focus.ring.shadow');
        outline: dt('treetable.node.toggle.button.focus.ring.width') dt('treetable.node.toggle.button.focus.ring.style') dt('treetable.node.toggle.button.focus.ring.color');
        outline-offset: dt('treetable.node.toggle.button.focus.ring.offset');
    }

    .p-treetable-node-toggle-icon:dir(rtl) {
        transform: rotate(180deg);
    }
`,sn={root:function(t){var n=t.instance,o=t.props;return["p-treetable p-component",{"p-treetable-hoverable":o.rowHover||n.rowSelectionMode,"p-treetable-resizable":o.resizableColumns,"p-treetable-resizable-fit":o.resizableColumns&&o.columnResizeMode==="fit","p-treetable-scrollable":o.scrollable,"p-treetable-flex-scrollable":o.scrollable&&o.scrollHeight==="flex","p-treetable-gridlines":o.showGridlines,"p-treetable-sm":o.size==="small","p-treetable-lg":o.size==="large"}]},loading:"p-treetable-loading",mask:"p-treetable-mask p-overlay-mask",loadingIcon:"p-treetable-loading-icon",header:"p-treetable-header",paginator:function(t){var n=t.position;return"p-treetable-paginator-"+n},tableContainer:"p-treetable-table-container",table:function(t){var n=t.props;return["p-treetable-table",{"p-treetable-scrollable-table":n.scrollable,"p-treetable-resizable-table":n.resizableColumns,"p-treetable-resizable-table-fit":n.resizableColumns&&n.columnResizeMode==="fit"}]},thead:"p-treetable-thead",headerCell:function(t){var n=t.instance,o=t.props;return["p-treetable-header-cell",{"p-treetable-sortable-column":n.columnProp("sortable"),"p-treetable-resizable-column":o.resizableColumns,"p-treetable-column-sorted":n.columnProp("sortable")?n.isColumnSorted():!1,"p-treetable-frozen-column":n.columnProp("frozen")}]},columnResizer:"p-treetable-column-resizer",columnHeaderContent:"p-treetable-column-header-content",columnTitle:"p-treetable-column-title",sortIcon:"p-treetable-sort-icon",pcSortBadge:"p-treetable-sort-badge",tbody:"p-treetable-tbody",row:function(t){var n=t.props,o=t.instance;return[{"p-treetable-row-selected":o.selected,"p-treetable-contextmenu-row-selected":n.contextMenuSelection&&o.isSelectedWithContextMenu}]},bodyCell:function(t){var n=t.instance;return[{"p-treetable-frozen-column":n.columnProp("frozen")}]},bodyCellContent:function(t){var n=t.instance;return["p-treetable-body-cell-content",{"p-treetable-body-cell-content-expander":n.columnProp("expander")}]},nodeToggleButton:"p-treetable-node-toggle-button",nodeToggleIcon:"p-treetable-node-toggle-icon",pcNodeCheckbox:"p-treetable-node-checkbox",emptyMessage:"p-treetable-empty-message",tfoot:"p-treetable-tfoot",footerCell:function(t){var n=t.instance;return[{"p-treetable-frozen-column":n.columnProp("frozen")}]},footer:"p-treetable-footer",columnResizeIndicator:"p-treetable-column-resize-indicator"},dn={tableContainer:{overflow:"auto"},thead:{position:"sticky"},tfoot:{position:"sticky"}},un=ye.extend({name:"treetable",style:an,classes:sn,inlineStyles:dn}),cn={name:"BaseTreeTable",extends:H,props:{value:{type:null,default:null},dataKey:{type:[String,Function],default:"key"},expandedKeys:{type:null,default:null},selectionKeys:{type:null,default:null},selectionMode:{type:String,default:null},metaKeySelection:{type:Boolean,default:!1},contextMenu:{type:Boolean,default:!1},contextMenuSelection:{type:Object,default:null},rows:{type:Number,default:0},first:{type:Number,default:0},totalRecords:{type:Number,default:0},paginator:{type:Boolean,default:!1},paginatorPosition:{type:String,default:"bottom"},alwaysShowPaginator:{type:Boolean,default:!0},paginatorTemplate:{type:String,default:"FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink RowsPerPageDropdown"},pageLinkSize:{type:Number,default:5},rowsPerPageOptions:{type:Array,default:null},currentPageReportTemplate:{type:String,default:"({currentPage} of {totalPages})"},lazy:{type:Boolean,default:!1},loading:{type:Boolean,default:!1},loadingIcon:{type:String,default:void 0},loadingMode:{type:String,default:"mask"},rowHover:{type:Boolean,default:!1},autoLayout:{type:Boolean,default:!1},sortField:{type:[String,Function],default:null},sortOrder:{type:Number,default:null},defaultSortOrder:{type:Number,default:1},multiSortMeta:{type:Array,default:null},sortMode:{type:String,default:"single"},removableSort:{type:Boolean,default:!1},filters:{type:Object,default:null},filterMode:{type:String,default:"lenient"},filterLocale:{type:String,default:void 0},resizableColumns:{type:Boolean,default:!1},columnResizeMode:{type:String,default:"fit"},indentation:{type:Number,default:1},showGridlines:{type:Boolean,default:!1},scrollable:{type:Boolean,default:!1},scrollHeight:{type:String,default:null},size:{type:String,default:null},tableStyle:{type:null,default:null},tableClass:{type:[String,Object],default:null},tableProps:{type:Object,default:null}},style:un,provide:function(){return{$pcTreeTable:this,$parentInstance:this}}},bt={name:"FooterCell",hostName:"TreeTable",extends:H,props:{column:{type:Object,default:null},index:{type:Number,default:null}},data:function(){return{styleObject:{}}},mounted:function(){this.columnProp("frozen")&&this.updateStickyPosition()},updated:function(){this.columnProp("frozen")&&this.updateStickyPosition()},methods:{columnProp:function(t){return de(this.column,t)},getColumnPT:function(t){var n,o={props:this.column.props,parent:{instance:this,props:this.$props,state:this.$data},context:{index:this.index,frozen:this.columnProp("frozen"),size:(n=this.$parentInstance)===null||n===void 0?void 0:n.size}};return f(this.ptm("column.".concat(t),{column:o}),this.ptm("column.".concat(t),o),this.ptmo(this.getColumnProp(),t,o))},getColumnProp:function(){return this.column.props&&this.column.props.pt?this.column.props.pt:void 0},updateStickyPosition:function(){if(this.columnProp("frozen")){var t=this.columnProp("alignFrozen");if(t==="right"){var n=0,o=xe(this.$el,'[data-p-frozen-column="true"]');o&&(n=Z(o)+parseFloat(o.style["inset-inline-end"]||0)),this.styleObject.insetInlineEnd=n+"px"}else{var r=0,l=Ie(this.$el,'[data-p-frozen-column="true"]');l&&(r=Z(l)+parseFloat(l.style["inset-inline-start"]||0)),this.styleObject.insetInlineStart=r+"px"}}}},computed:{containerClass:function(){return[this.columnProp("footerClass"),this.columnProp("class"),this.cx("footerCell")]},containerStyle:function(){var t=this.columnProp("footerStyle"),n=this.columnProp("style");return this.columnProp("frozen")?[n,t,this.styleObject]:[n,t]}}};function le(e){"@babel/helpers - typeof";return le=typeof Symbol=="function"&&typeof Symbol.iterator=="symbol"?function(t){return typeof t}:function(t){return t&&typeof Symbol=="function"&&t.constructor===Symbol&&t!==Symbol.prototype?"symbol":typeof t},le(e)}function Ye(e,t){var n=Object.keys(e);if(Object.getOwnPropertySymbols){var o=Object.getOwnPropertySymbols(e);t&&(o=o.filter(function(r){return Object.getOwnPropertyDescriptor(e,r).enumerable})),n.push.apply(n,o)}return n}function Xe(e){for(var t=1;t<arguments.length;t++){var n=arguments[t]!=null?arguments[t]:{};t%2?Ye(Object(n),!0).forEach(function(o){pn(e,o,n[o])}):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(n)):Ye(Object(n)).forEach(function(o){Object.defineProperty(e,o,Object.getOwnPropertyDescriptor(n,o))})}return e}function pn(e,t,n){return(t=fn(t))in e?Object.defineProperty(e,t,{value:n,enumerable:!0,configurable:!0,writable:!0}):e[t]=n,e}function fn(e){var t=hn(e,"string");return le(t)=="symbol"?t:t+""}function hn(e,t){if(le(e)!="object"||!e)return e;var n=e[Symbol.toPrimitive];if(n!==void 0){var o=n.call(e,t);if(le(o)!="object")return o;throw new TypeError("@@toPrimitive must return a primitive value.")}return(t==="string"?String:Number)(e)}var mn=["data-p-frozen-column"];function bn(e,t,n,o,r,l){return a(),m("td",f({style:l.containerStyle,class:l.containerClass,role:"cell"},Xe(Xe({},l.getColumnPT("root")),l.getColumnPT("footerCell")),{"data-p-frozen-column":l.columnProp("frozen")}),[n.column.children&&n.column.children.footer?(a(),g(x(n.column.children.footer),{key:0,column:n.column},null,8,["column"])):w("",!0),l.columnProp("footer")?(a(),m("span",f({key:1,class:e.cx("columnFooter")},l.getColumnPT("columnFooter")),j(l.columnProp("footer")),17)):w("",!0)],16,mn)}bt.render=bn;var gt={name:"HeaderCell",hostName:"TreeTable",extends:H,emits:["column-click","column-resizestart"],props:{column:{type:Object,default:null},resizableColumns:{type:Boolean,default:!1},sortField:{type:[String,Function],default:null},sortOrder:{type:Number,default:null},multiSortMeta:{type:Array,default:null},sortMode:{type:String,default:"single"},index:{type:Number,default:null}},data:function(){return{styleObject:{}}},mounted:function(){this.columnProp("frozen")&&this.updateStickyPosition()},updated:function(){this.columnProp("frozen")&&this.updateStickyPosition()},methods:{columnProp:function(t){return de(this.column,t)},getColumnPT:function(t){var n,o={props:this.column.props,parent:{instance:this,props:this.$props,state:this.$data},context:{index:this.index,sorted:this.isColumnSorted(),frozen:this.$parentInstance.scrollable&&this.columnProp("frozen"),resizable:this.resizableColumns,scrollable:this.$parentInstance.scrollable,showGridlines:this.$parentInstance.showGridlines,size:(n=this.$parentInstance)===null||n===void 0?void 0:n.size}};return f(this.ptm("column.".concat(t),{column:o}),this.ptm("column.".concat(t),o),this.ptmo(this.getColumnProp(),t,o))},getColumnProp:function(){return this.column.props&&this.column.props.pt?this.column.props.pt:void 0},updateStickyPosition:function(){if(this.columnProp("frozen")){var t=this.columnProp("alignFrozen");if(t==="right"){var n=0,o=xe(this.$el,'[data-p-frozen-column="true"]');o&&(n=Z(o)+parseFloat(o.style["inset-inline-end"]||0)),this.styleObject.insetInlineEnd=n+"px"}else{var r=0,l=Ie(this.$el,'[data-p-frozen-column="true"]');l&&(r=Z(l)+parseFloat(l.style["inset-inline-start"]||0)),this.styleObject.insetInlineStart=r+"px"}var s=this.$el.parentElement.nextElementSibling;if(s){var d=ct(this.$el);s.children[d].style["inset-inline-start"]=this.styleObject["inset-inline-start"],s.children[d].style["inset-inline-end"]=this.styleObject["inset-inline-end"]}}},onClick:function(t){this.$emit("column-click",{originalEvent:t,column:this.column})},onKeyDown:function(t){(t.code==="Enter"||t.code==="NumpadEnter"||t.code==="Space")&&t.currentTarget.nodeName==="TH"&&L(t.currentTarget,"data-p-sortable-column")&&(this.$emit("column-click",{originalEvent:t,column:this.column}),t.preventDefault())},onResizeStart:function(t){this.$emit("column-resizestart",t)},getMultiSortMetaIndex:function(){for(var t=-1,n=0;n<this.multiSortMeta.length;n++){var o=this.multiSortMeta[n];if(o.field===this.columnProp("field")||o.field===this.columnProp("sortField")){t=n;break}}return t},isMultiSorted:function(){return this.columnProp("sortable")&&this.getMultiSortMetaIndex()>-1},isColumnSorted:function(){return this.sortMode==="single"?this.sortField&&(this.sortField===this.columnProp("field")||this.sortField===this.columnProp("sortField")):this.isMultiSorted()}},computed:{containerClass:function(){return[this.columnProp("headerClass"),this.columnProp("class"),this.cx("headerCell")]},containerStyle:function(){var t=this.columnProp("headerStyle"),n=this.columnProp("style");return this.columnProp("frozen")?[n,t,this.styleObject]:[n,t]},sortState:function(){var t=!1,n=null;if(this.sortMode==="single")t=this.sortField&&(this.sortField===this.columnProp("field")||this.sortField===this.columnProp("sortField")),n=t?this.sortOrder:0;else if(this.sortMode==="multiple"){var o=this.getMultiSortMetaIndex();o>-1&&(t=!0,n=this.multiSortMeta[o].order)}return{sorted:t,sortOrder:n}},sortableColumnIcon:function(){var t=this.sortState,n=t.sorted,o=t.sortOrder;if(n){if(n&&o>0)return je;if(n&&o<0)return Ee}else return Le;return null},ariaSort:function(){if(this.columnProp("sortable")){var t=this.sortState,n=t.sorted,o=t.sortOrder;return n&&o<0?"descending":n&&o>0?"ascending":"none"}else return null}},components:{Badge:at,SortAltIcon:Le,SortAmountUpAltIcon:je,SortAmountDownIcon:Ee}};function oe(e){"@babel/helpers - typeof";return oe=typeof Symbol=="function"&&typeof Symbol.iterator=="symbol"?function(t){return typeof t}:function(t){return t&&typeof Symbol=="function"&&t.constructor===Symbol&&t!==Symbol.prototype?"symbol":typeof t},oe(e)}function qe(e,t){var n=Object.keys(e);if(Object.getOwnPropertySymbols){var o=Object.getOwnPropertySymbols(e);t&&(o=o.filter(function(r){return Object.getOwnPropertyDescriptor(e,r).enumerable})),n.push.apply(n,o)}return n}function Ze(e){for(var t=1;t<arguments.length;t++){var n=arguments[t]!=null?arguments[t]:{};t%2?qe(Object(n),!0).forEach(function(o){gn(e,o,n[o])}):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(n)):qe(Object(n)).forEach(function(o){Object.defineProperty(e,o,Object.getOwnPropertyDescriptor(n,o))})}return e}function gn(e,t,n){return(t=yn(t))in e?Object.defineProperty(e,t,{value:n,enumerable:!0,configurable:!0,writable:!0}):e[t]=n,e}function yn(e){var t=vn(e,"string");return oe(t)=="symbol"?t:t+""}function vn(e,t){if(oe(e)!="object"||!e)return e;var n=e[Symbol.toPrimitive];if(n!==void 0){var o=n.call(e,t);if(oe(o)!="object")return o;throw new TypeError("@@toPrimitive must return a primitive value.")}return(t==="string"?String:Number)(e)}var Cn=["tabindex","aria-sort","data-p-sortable-column","data-p-resizable-column","data-p-sorted","data-p-frozen-column"];function kn(e,t,n,o,r,l){var s=E("Badge");return a(),m("th",f({class:l.containerClass,style:[l.containerStyle],onClick:t[1]||(t[1]=function(){return l.onClick&&l.onClick.apply(l,arguments)}),onKeydown:t[2]||(t[2]=function(){return l.onKeyDown&&l.onKeyDown.apply(l,arguments)}),tabindex:l.columnProp("sortable")?"0":null,"aria-sort":l.ariaSort,role:"columnheader"},Ze(Ze({},l.getColumnPT("root")),l.getColumnPT("headerCell")),{"data-p-sortable-column":l.columnProp("sortable"),"data-p-resizable-column":n.resizableColumns,"data-p-sorted":l.isColumnSorted(),"data-p-frozen-column":l.columnProp("frozen")}),[n.resizableColumns&&!l.columnProp("frozen")?(a(),m("span",f({key:0,class:e.cx("columnResizer"),onMousedown:t[0]||(t[0]=function(){return l.onResizeStart&&l.onResizeStart.apply(l,arguments)})},l.getColumnPT("columnResizer")),null,16)):w("",!0),v("div",f({class:e.cx("columnHeaderContent")},l.getColumnPT("columnHeaderContent")),[n.column.children&&n.column.children.header?(a(),g(x(n.column.children.header),{key:0,column:n.column},null,8,["column"])):w("",!0),l.columnProp("header")?(a(),m("span",f({key:1,class:e.cx("columnTitle")},l.getColumnPT("columnTitle")),j(l.columnProp("header")),17)):w("",!0),l.columnProp("sortable")?(a(),m("span",Ke(f({key:2},l.getColumnPT("sort"))),[(a(),g(x(n.column.children&&n.column.children.sorticon||l.sortableColumnIcon),f({sorted:l.sortState.sorted,sortOrder:l.sortState.sortOrder,class:e.cx("sortIcon")},l.getColumnPT("sortIcon")),null,16,["sorted","sortOrder","class"]))],16)):w("",!0),l.isMultiSorted()?(a(),g(s,f({key:3,class:e.cx("pcSortBadge")},l.getColumnPT("pcSortBadge"),{value:l.getMultiSortMetaIndex()+1,size:"small"}),null,16,["class","value"])):w("",!0)],16)],16,Cn)}gt.render=kn;var yt={name:"BodyCell",hostName:"TreeTable",extends:H,emits:["node-toggle","checkbox-toggle"],props:{node:{type:Object,default:null},column:{type:Object,default:null},level:{type:Number,default:0},indentation:{type:Number,default:1},leaf:{type:Boolean,default:!1},expanded:{type:Boolean,default:!1},selectionMode:{type:String,default:null},checked:{type:Boolean,default:!1},partialChecked:{type:Boolean,default:!1},templates:{type:Object,default:null},index:{type:Number,default:null},loadingMode:{type:String,default:"mask"}},data:function(){return{styleObject:{}}},mounted:function(){this.columnProp("frozen")&&this.updateStickyPosition()},updated:function(){this.columnProp("frozen")&&this.updateStickyPosition()},methods:{toggle:function(){this.$emit("node-toggle",this.node)},columnProp:function(t){return de(this.column,t)},getColumnPT:function(t){var n,o={props:this.column.props,parent:{instance:this,props:this.$props,state:this.$data},context:{index:this.index,selectable:this.$parentInstance.rowHover||this.$parentInstance.rowSelectionMode,selected:this.$parent.selected,frozen:this.columnProp("frozen"),scrollable:this.$parentInstance.scrollable,showGridlines:this.$parentInstance.showGridlines,size:(n=this.$parentInstance)===null||n===void 0?void 0:n.size,node:this.node}};return f(this.ptm("column.".concat(t),{column:o}),this.ptm("column.".concat(t),o),this.ptmo(this.getColumnProp(),t,o))},getColumnProp:function(){return this.column.props&&this.column.props.pt?this.column.props.pt:void 0},getColumnCheckboxPT:function(t){var n={props:this.column.props,parent:{instance:this,props:this.$props,state:this.$data},context:{checked:this.checked,partialChecked:this.partialChecked,node:this.node}};return f(this.ptm("column.".concat(t),{column:n}),this.ptm("column.".concat(t),n),this.ptmo(this.getColumnProp(),t,n))},updateStickyPosition:function(){if(this.columnProp("frozen")){var t=this.columnProp("alignFrozen");if(t==="right"){var n=0,o=xe(this.$el,'[data-p-frozen-column="true"]');o&&(n=Z(o)+parseFloat(o.style["inset-inline-end"]||0)),this.styleObject.insetInlineEnd=n+"px"}else{var r=0,l=Ie(this.$el,'[data-p-frozen-column="true"]');l&&(r=Z(l)+parseFloat(l.style["inset-inline-start"]||0)),this.styleObject.insetInlineStart=r+"px"}}},resolveFieldData:function(t,n){return G(t,n)},toggleCheckbox:function(){this.$emit("checkbox-toggle")}},computed:{containerClass:function(){return[this.columnProp("bodyClass"),this.columnProp("class"),this.cx("bodyCell")]},containerStyle:function(){var t=this.columnProp("bodyStyle"),n=this.columnProp("style");return this.columnProp("frozen")?[n,t,this.styleObject]:[n,t]},togglerStyle:function(){return{marginLeft:this.level*this.indentation+"rem",visibility:this.leaf?"hidden":"visible"}},checkboxSelectionMode:function(){return this.selectionMode==="checkbox"}},components:{Checkbox:Vt,ChevronRightIcon:$t,ChevronDownIcon:Ut,CheckIcon:At,MinusIcon:Dt,SpinnerIcon:it},directives:{ripple:st}};function re(e){"@babel/helpers - typeof";return re=typeof Symbol=="function"&&typeof Symbol.iterator=="symbol"?function(t){return typeof t}:function(t){return t&&typeof Symbol=="function"&&t.constructor===Symbol&&t!==Symbol.prototype?"symbol":typeof t},re(e)}function Qe(e,t){var n=Object.keys(e);if(Object.getOwnPropertySymbols){var o=Object.getOwnPropertySymbols(e);t&&(o=o.filter(function(r){return Object.getOwnPropertyDescriptor(e,r).enumerable})),n.push.apply(n,o)}return n}function Je(e){for(var t=1;t<arguments.length;t++){var n=arguments[t]!=null?arguments[t]:{};t%2?Qe(Object(n),!0).forEach(function(o){wn(e,o,n[o])}):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(n)):Qe(Object(n)).forEach(function(o){Object.defineProperty(e,o,Object.getOwnPropertyDescriptor(n,o))})}return e}function wn(e,t,n){return(t=Sn(t))in e?Object.defineProperty(e,t,{value:n,enumerable:!0,configurable:!0,writable:!0}):e[t]=n,e}function Sn(e){var t=Pn(e,"string");return re(t)=="symbol"?t:t+""}function Pn(e,t){if(re(e)!="object"||!e)return e;var n=e[Symbol.toPrimitive];if(n!==void 0){var o=n.call(e,t);if(re(o)!="object")return o;throw new TypeError("@@toPrimitive must return a primitive value.")}return(t==="string"?String:Number)(e)}var zn=["data-p-frozen-column"];function Mn(e,t,n,o,r,l){var s=E("SpinnerIcon"),d=E("Checkbox"),u=Zt("ripple");return a(),m("td",f({style:l.containerStyle,class:l.containerClass,role:"cell"},Je(Je({},l.getColumnPT("root")),l.getColumnPT("bodyCell")),{"data-p-frozen-column":l.columnProp("frozen")}),[v("div",f({class:e.cx("bodyCellContent")},l.getColumnPT("bodyCellContent")),[l.columnProp("expander")?Qt((a(),m("button",f({key:0,type:"button",class:e.cx("nodeToggleButton"),onClick:t[0]||(t[0]=function(){return l.toggle&&l.toggle.apply(l,arguments)}),style:l.togglerStyle,tabindex:"-1"},l.getColumnPT("nodeToggleButton"),{"data-pc-group-section":"rowactionbutton"}),[n.node.loading&&n.loadingMode==="icon"?(a(),m(O,{key:0},[n.templates.nodetoggleicon?(a(),g(x(n.templates.nodetoggleicon),{key:0})):w("",!0),n.templates.nodetogglericon?(a(),g(x(n.templates.nodetogglericon),{key:1})):(a(),g(s,f({key:2,spin:""},e.ptm("nodetoggleicon")),null,16))],64)):(a(),m(O,{key:1},[n.column.children&&n.column.children.rowtoggleicon?(a(),g(x(n.column.children.rowtoggleicon),{key:0,node:n.node,expanded:n.expanded,class:M(e.cx("nodeToggleIcon"))},null,8,["node","expanded","class"])):n.templates.nodetoggleicon?(a(),g(x(n.templates.nodetoggleicon),{key:1,node:n.node,expanded:n.expanded,class:M(e.cx("nodeToggleIcon"))},null,8,["node","expanded","class"])):n.column.children&&n.column.children.rowtogglericon?(a(),g(x(n.column.children.rowtogglericon),{key:2,node:n.node,expanded:n.expanded,class:M(e.cx("nodeToggleIcon"))},null,8,["node","expanded","class"])):n.expanded?(a(),g(x(n.node.expandedIcon?"span":"ChevronDownIcon"),f({key:3,class:e.cx("nodeToggleIcon")},l.getColumnPT("nodeToggleIcon")),null,16,["class"])):(a(),g(x(n.node.collapsedIcon?"span":"ChevronRightIcon"),f({key:4,class:e.cx("nodeToggleIcon")},l.getColumnPT("nodeToggleIcon")),null,16,["class"]))],64))],16)),[[u]]):w("",!0),l.checkboxSelectionMode&&l.columnProp("expander")?(a(),g(d,{key:1,modelValue:n.checked,binary:!0,class:M(e.cx("pcNodeCheckbox")),disabled:n.node.selectable===!1,onChange:l.toggleCheckbox,tabindex:-1,indeterminate:n.partialChecked,unstyled:e.unstyled,pt:l.getColumnCheckboxPT("pcNodeCheckbox"),"data-p-partialchecked":n.partialChecked},{icon:y(function(c){return[n.templates.checkboxicon?(a(),g(x(n.templates.checkboxicon),{key:0,checked:c.checked,partialChecked:n.partialChecked,class:M(c.class)},null,8,["checked","partialChecked","class"])):w("",!0)]}),_:1},8,["modelValue","class","disabled","onChange","indeterminate","unstyled","pt","data-p-partialchecked"])):w("",!0),n.column.children&&n.column.children.body?(a(),g(x(n.column.children.body),{key:2,node:n.node,column:n.column},null,8,["node","column"])):(a(),m(O,{key:3},[be(j(l.resolveFieldData(n.node.data,l.columnProp("field"))),1)],64))],16)],16,zn)}yt.render=Mn;function ie(e){"@babel/helpers - typeof";return ie=typeof Symbol=="function"&&typeof Symbol.iterator=="symbol"?function(t){return typeof t}:function(t){return t&&typeof Symbol=="function"&&t.constructor===Symbol&&t!==Symbol.prototype?"symbol":typeof t},ie(e)}function we(e,t){var n=typeof Symbol<"u"&&e[Symbol.iterator]||e["@@iterator"];if(!n){if(Array.isArray(e)||(n=vt(e))||t){n&&(e=n);var o=0,r=function(){};return{s:r,n:function(){return o>=e.length?{done:!0}:{done:!1,value:e[o++]}},e:function(c){throw c},f:r}}throw new TypeError(`Invalid attempt to iterate non-iterable instance.
In order to be iterable, non-array objects must have a [Symbol.iterator]() method.`)}var l,s=!0,d=!1;return{s:function(){n=n.call(e)},n:function(){var c=n.next();return s=c.done,c},e:function(c){d=!0,l=c},f:function(){try{s||n.return==null||n.return()}finally{if(d)throw l}}}}function _e(e,t){var n=Object.keys(e);if(Object.getOwnPropertySymbols){var o=Object.getOwnPropertySymbols(e);t&&(o=o.filter(function(r){return Object.getOwnPropertyDescriptor(e,r).enumerable})),n.push.apply(n,o)}return n}function Se(e){for(var t=1;t<arguments.length;t++){var n=arguments[t]!=null?arguments[t]:{};t%2?_e(Object(n),!0).forEach(function(o){Tn(e,o,n[o])}):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(n)):_e(Object(n)).forEach(function(o){Object.defineProperty(e,o,Object.getOwnPropertyDescriptor(n,o))})}return e}function Tn(e,t,n){return(t=Fn(t))in e?Object.defineProperty(e,t,{value:n,enumerable:!0,configurable:!0,writable:!0}):e[t]=n,e}function Fn(e){var t=On(e,"string");return ie(t)=="symbol"?t:t+""}function On(e,t){if(ie(e)!="object"||!e)return e;var n=e[Symbol.toPrimitive];if(n!==void 0){var o=n.call(e,t);if(ie(o)!="object")return o;throw new TypeError("@@toPrimitive must return a primitive value.")}return(t==="string"?String:Number)(e)}function et(e){return Kn(e)||In(e)||vt(e)||xn()}function xn(){throw new TypeError(`Invalid attempt to spread non-iterable instance.
In order to be iterable, non-array objects must have a [Symbol.iterator]() method.`)}function vt(e,t){if(e){if(typeof e=="string")return Me(e,t);var n={}.toString.call(e).slice(8,-1);return n==="Object"&&e.constructor&&(n=e.constructor.name),n==="Map"||n==="Set"?Array.from(e):n==="Arguments"||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)?Me(e,t):void 0}}function In(e){if(typeof Symbol<"u"&&e[Symbol.iterator]!=null||e["@@iterator"]!=null)return Array.from(e)}function Kn(e){if(Array.isArray(e))return Me(e)}function Me(e,t){(t==null||t>e.length)&&(t=e.length);for(var n=0,o=Array(t);n<t;n++)o[n]=e[n];return o}var Ct={name:"TreeTableRow",hostName:"TreeTable",extends:H,emits:["node-click","node-toggle","checkbox-change","nodeClick","nodeToggle","checkboxChange","row-rightclick","rowRightclick"],props:{node:{type:null,default:null},dataKey:{type:[String,Function],default:"key"},parentNode:{type:null,default:null},columns:{type:null,default:null},expandedKeys:{type:null,default:null},selectionKeys:{type:null,default:null},selectionMode:{type:String,default:null},level:{type:Number,default:0},indentation:{type:Number,default:1},tabindex:{type:Number,default:-1},ariaSetSize:{type:Number,default:null},ariaPosInset:{type:Number,default:null},loadingMode:{type:String,default:"mask"},templates:{type:Object,default:null},contextMenu:{type:Boolean,default:!1},contextMenuSelection:{type:Object,default:null}},nodeTouched:!1,methods:{columnProp:function(t,n){return de(t,n)},toggle:function(){this.$emit("node-toggle",this.node)},onClick:function(t){Ve(t.target)||L(t.target,"data-pc-section")==="nodetogglebutton"||L(t.target,"data-pc-section")==="nodetoggleicon"||t.target.tagName==="path"||(this.setTabIndexForSelectionMode(t,this.nodeTouched),this.$emit("node-click",{originalEvent:t,nodeTouched:this.nodeTouched,node:this.node}),this.nodeTouched=!1)},onRowRightClick:function(t){this.$emit("row-rightclick",{originalEvent:t,node:this.node})},onTouchEnd:function(){this.nodeTouched=!0},nodeKey:function(t){return G(t,this.dataKey)},onKeyDown:function(t,n){switch(t.code){case"ArrowDown":this.onArrowDownKey(t);break;case"ArrowUp":this.onArrowUpKey(t);break;case"ArrowLeft":this.onArrowLeftKey(t);break;case"ArrowRight":this.onArrowRightKey(t);break;case"Home":this.onHomeKey(t);break;case"End":this.onEndKey(t);break;case"Enter":case"NumpadEnter":case"Space":Ve(t.target)||this.onEnterKey(t,n);break;case"Tab":this.onTabKey(t);break}},onArrowDownKey:function(t){var n=t.currentTarget.nextElementSibling;n&&this.focusRowChange(t.currentTarget,n),t.preventDefault()},onArrowUpKey:function(t){var n=t.currentTarget.previousElementSibling;n&&this.focusRowChange(t.currentTarget,n),t.preventDefault()},onArrowRightKey:function(t){var n=this,o=te(t.currentTarget,"button").style.visibility==="hidden",r=te(this.$refs.node,'[data-pc-section="nodetogglebutton"]');o||(!this.expanded&&r.click(),this.$nextTick(function(){n.onArrowDownKey(t)}),t.preventDefault())},onArrowLeftKey:function(t){if(!(this.level===0&&!this.expanded)){var n=t.currentTarget,o=te(n,"button").style.visibility==="hidden",r=te(n,'[data-pc-section="nodetogglebutton"]');if(this.expanded&&!o){r.click();return}var l=this.findBeforeClickableNode(n);l&&this.focusRowChange(n,l)}},onHomeKey:function(t){var n=te(t.currentTarget.parentElement,'tr[aria-level="'.concat(this.level+1,'"]'));n&&ke(n),t.preventDefault()},onEndKey:function(t){var n=he(t.currentTarget.parentElement,'tr[aria-level="'.concat(this.level+1,'"]')),o=n[n.length-1];ke(o),t.preventDefault()},onEnterKey:function(t){if(t.preventDefault(),this.setTabIndexForSelectionMode(t,this.nodeTouched),this.selectionMode==="checkbox"){this.toggleCheckbox();return}this.$emit("node-click",{originalEvent:t,nodeTouched:this.nodeTouched,node:this.node}),this.nodeTouched=!1},onTabKey:function(){var t=et(he(this.$refs.node.parentElement,"tr")),n=t.some(function(r){return L(r,"data-p-selected")||r.getAttribute("aria-checked")==="true"});if(t.forEach(function(r){r.tabIndex=-1}),n){var o=t.filter(function(r){return L(r,"data-p-selected")||r.getAttribute("aria-checked")==="true"});o[0].tabIndex=0;return}t[0].tabIndex=0},focusRowChange:function(t,n){t.tabIndex="-1",n.tabIndex="0",ke(n)},findBeforeClickableNode:function(t){var n=t.previousElementSibling;if(n){var o=n.querySelector("button");return o&&o.style.visibility!=="hidden"?n:this.findBeforeClickableNode(n)}return null},toggleCheckbox:function(){var t=this.selectionKeys?Se({},this.selectionKeys):{},n=!this.checked;this.propagateDown(this.node,n,t),this.$emit("checkbox-change",{node:this.node,check:n,selectionKeys:t})},propagateDown:function(t,n,o){if(n?o[this.nodeKey(t)]={checked:!0,partialChecked:!1}:delete o[this.nodeKey(t)],t.children&&t.children.length){var r=we(t.children),l;try{for(r.s();!(l=r.n()).done;){var s=l.value;this.propagateDown(s,n,o)}}catch(d){r.e(d)}finally{r.f()}}},propagateUp:function(t){var n=t.check,o=Se({},t.selectionKeys),r=0,l=!1,s=we(this.node.children),d;try{for(s.s();!(d=s.n()).done;){var u=d.value;o[this.nodeKey(u)]&&o[this.nodeKey(u)].checked?r++:o[this.nodeKey(u)]&&o[this.nodeKey(u)].partialChecked&&(l=!0)}}catch(c){s.e(c)}finally{s.f()}n&&r===this.node.children.length?o[this.nodeKey(this.node)]={checked:!0,partialChecked:!1}:(n||delete o[this.nodeKey(this.node)],l||r>0&&r!==this.node.children.length?o[this.nodeKey(this.node)]={checked:!1,partialChecked:!0}:o[this.nodeKey(this.node)]={checked:!1,partialChecked:!1}),this.$emit("checkbox-change",{node:t.node,check:t.check,selectionKeys:o})},onCheckboxChange:function(t){var n=t.check,o=Se({},t.selectionKeys),r=0,l=!1,s=we(this.node.children),d;try{for(s.s();!(d=s.n()).done;){var u=d.value;o[this.nodeKey(u)]&&o[this.nodeKey(u)].checked?r++:o[this.nodeKey(u)]&&o[this.nodeKey(u)].partialChecked&&(l=!0)}}catch(c){s.e(c)}finally{s.f()}n&&r===this.node.children.length?o[this.nodeKey(this.node)]={checked:!0,partialChecked:!1}:(n||delete o[this.nodeKey(this.node)],l||r>0&&r!==this.node.children.length?o[this.nodeKey(this.node)]={checked:!1,partialChecked:!0}:o[this.nodeKey(this.node)]={checked:!1,partialChecked:!1}),this.$emit("checkbox-change",{node:t.node,check:t.check,selectionKeys:o})},setTabIndexForSelectionMode:function(t,n){if(this.selectionMode!==null){var o=et(he(this.$refs.node.parentElement,"tr"));t.currentTarget.tabIndex=n===!1?-1:0,o.every(function(r){return r.tabIndex===-1})&&(o[0].tabIndex=0)}}},computed:{containerClass:function(){return[this.node.styleClass,this.cx("row")]},expanded:function(){return this.expandedKeys&&this.expandedKeys[this.nodeKey(this.node)]===!0},leaf:function(){return this.node.leaf===!1?!1:!(this.node.children&&this.node.children.length)},selected:function(){return this.selectionMode&&this.selectionKeys?this.selectionKeys[this.nodeKey(this.node)]===!0:!1},isSelectedWithContextMenu:function(){return this.node&&this.contextMenuSelection?Yt(this.node,this.contextMenuSelection,this.dataKey):!1},checked:function(){return this.selectionKeys?this.selectionKeys[this.nodeKey(this.node)]&&this.selectionKeys[this.nodeKey(this.node)].checked:!1},partialChecked:function(){return this.selectionKeys?this.selectionKeys[this.nodeKey(this.node)]&&this.selectionKeys[this.nodeKey(this.node)].partialChecked:!1},getAriaSelected:function(){return this.selectionMode==="single"||this.selectionMode==="multiple"?this.selected:null},ptmOptions:function(){return{context:{selectable:this.$parentInstance.rowHover||this.$parentInstance.rowSelectionMode,selected:this.selected,scrollable:this.$parentInstance.scrollable}}}},components:{TTBodyCell:yt}},Bn=["tabindex","aria-expanded","aria-level","aria-setsize","aria-posinset","aria-selected","aria-checked","data-p-selected","data-p-selected-contextmenu"];function En(e,t,n,o,r,l){var s=E("TTBodyCell"),d=E("TreeTableRow",!0);return a(),m(O,null,[v("tr",f({ref:"node",class:l.containerClass,style:n.node.style,tabindex:n.tabindex,role:"row","aria-expanded":n.node.children&&n.node.children.length?l.expanded:void 0,"aria-level":n.level+1,"aria-setsize":n.ariaSetSize,"aria-posinset":n.ariaPosInset,"aria-selected":l.getAriaSelected,"aria-checked":l.checked||void 0,onClick:t[1]||(t[1]=function(){return l.onClick&&l.onClick.apply(l,arguments)}),onKeydown:t[2]||(t[2]=function(){return l.onKeyDown&&l.onKeyDown.apply(l,arguments)}),onTouchend:t[3]||(t[3]=function(){return l.onTouchEnd&&l.onTouchEnd.apply(l,arguments)}),onContextmenu:t[4]||(t[4]=function(){return l.onRowRightClick&&l.onRowRightClick.apply(l,arguments)})},e.ptm("row",l.ptmOptions),{"data-p-selected":l.selected,"data-p-selected-contextmenu":n.contextMenuSelection&&l.isSelectedWithContextMenu}),[(a(!0),m(O,null,U(n.columns,function(u,c){return a(),m(O,{key:l.columnProp(u,"columnKey")||l.columnProp(u,"field")||c},[l.columnProp(u,"hidden")?w("",!0):(a(),g(s,{key:0,column:u,node:n.node,level:n.level,leaf:l.leaf,indentation:n.indentation,expanded:l.expanded,selectionMode:n.selectionMode,checked:l.checked,partialChecked:l.partialChecked,templates:n.templates,onNodeToggle:t[0]||(t[0]=function(i){return e.$emit("node-toggle",i)}),onCheckboxToggle:l.toggleCheckbox,index:c,loadingMode:n.loadingMode,unstyled:e.unstyled,pt:e.pt},null,8,["column","node","level","leaf","indentation","expanded","selectionMode","checked","partialChecked","templates","onCheckboxToggle","index","loadingMode","unstyled","pt"]))],64)}),128))],16,Bn),l.expanded&&n.node.children&&n.node.children.length?(a(!0),m(O,{key:0},U(n.node.children,function(u){return a(),g(d,{key:l.nodeKey(u),dataKey:n.dataKey,columns:n.columns,node:u,parentNode:n.node,level:n.level+1,expandedKeys:n.expandedKeys,selectionMode:n.selectionMode,selectionKeys:n.selectionKeys,contextMenu:n.contextMenu,contextMenuSelection:n.contextMenuSelection,indentation:n.indentation,ariaPosInset:n.node.children.indexOf(u)+1,ariaSetSize:n.node.children.length,templates:n.templates,onNodeToggle:t[5]||(t[5]=function(c){return e.$emit("node-toggle",c)}),onNodeClick:t[6]||(t[6]=function(c){return e.$emit("node-click",c)}),onRowRightclick:t[7]||(t[7]=function(c){return e.$emit("row-rightclick",c)}),onCheckboxChange:l.onCheckboxChange,unstyled:e.unstyled,pt:e.pt},null,8,["dataKey","columns","node","parentNode","level","expandedKeys","selectionMode","selectionKeys","contextMenu","contextMenuSelection","indentation","ariaPosInset","ariaSetSize","templates","onCheckboxChange","unstyled","pt"])}),128)):w("",!0)],64)}Ct.render=En;function ae(e){"@babel/helpers - typeof";return ae=typeof Symbol=="function"&&typeof Symbol.iterator=="symbol"?function(t){return typeof t}:function(t){return t&&typeof Symbol=="function"&&t.constructor===Symbol&&t!==Symbol.prototype?"symbol":typeof t},ae(e)}function pe(e,t){var n=typeof Symbol<"u"&&e[Symbol.iterator]||e["@@iterator"];if(!n){if(Array.isArray(e)||(n=kt(e))||t){n&&(e=n);var o=0,r=function(){};return{s:r,n:function(){return o>=e.length?{done:!0}:{done:!1,value:e[o++]}},e:function(c){throw c},f:r}}throw new TypeError(`Invalid attempt to iterate non-iterable instance.
In order to be iterable, non-array objects must have a [Symbol.iterator]() method.`)}var l,s=!0,d=!1;return{s:function(){n=n.call(e)},n:function(){var c=n.next();return s=c.done,c},e:function(c){d=!0,l=c},f:function(){try{s||n.return==null||n.return()}finally{if(d)throw l}}}}function tt(e,t){var n=Object.keys(e);if(Object.getOwnPropertySymbols){var o=Object.getOwnPropertySymbols(e);t&&(o=o.filter(function(r){return Object.getOwnPropertyDescriptor(e,r).enumerable})),n.push.apply(n,o)}return n}function W(e){for(var t=1;t<arguments.length;t++){var n=arguments[t]!=null?arguments[t]:{};t%2?tt(Object(n),!0).forEach(function(o){jn(e,o,n[o])}):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(n)):tt(Object(n)).forEach(function(o){Object.defineProperty(e,o,Object.getOwnPropertyDescriptor(n,o))})}return e}function jn(e,t,n){return(t=Ln(t))in e?Object.defineProperty(e,t,{value:n,enumerable:!0,configurable:!0,writable:!0}):e[t]=n,e}function Ln(e){var t=Rn(e,"string");return ae(t)=="symbol"?t:t+""}function Rn(e,t){if(ae(e)!="object"||!e)return e;var n=e[Symbol.toPrimitive];if(n!==void 0){var o=n.call(e,t);if(ae(o)!="object")return o;throw new TypeError("@@toPrimitive must return a primitive value.")}return(t==="string"?String:Number)(e)}function ne(e){return Un(e)||An(e)||kt(e)||Dn()}function Dn(){throw new TypeError(`Invalid attempt to spread non-iterable instance.
In order to be iterable, non-array objects must have a [Symbol.iterator]() method.`)}function kt(e,t){if(e){if(typeof e=="string")return Te(e,t);var n={}.toString.call(e).slice(8,-1);return n==="Object"&&e.constructor&&(n=e.constructor.name),n==="Map"||n==="Set"?Array.from(e):n==="Arguments"||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)?Te(e,t):void 0}}function An(e){if(typeof Symbol<"u"&&e[Symbol.iterator]!=null||e["@@iterator"]!=null)return Array.from(e)}function Un(e){if(Array.isArray(e))return Te(e)}function Te(e,t){(t==null||t>e.length)&&(t=e.length);for(var n=0,o=Array(t);n<t;n++)o[n]=e[n];return o}var wt={name:"TreeTable",extends:cn,inheritAttrs:!1,emits:["node-expand","node-collapse","update:expandedKeys","update:selectionKeys","node-select","node-unselect","update:first","update:rows","page","update:sortField","update:sortOrder","update:multiSortMeta","sort","filter","column-resize-end","update:contextMenuSelection","row-contextmenu"],provide:function(){return{$columns:this.d_columns}},data:function(){return{d_expandedKeys:this.expandedKeys||{},d_first:this.first,d_rows:this.rows,d_sortField:this.sortField,d_sortOrder:this.sortOrder,d_multiSortMeta:this.multiSortMeta?ne(this.multiSortMeta):[],hasASelectedNode:!1,d_columns:new qt({type:"Column"})}},documentColumnResizeListener:null,documentColumnResizeEndListener:null,lastResizeHelperX:null,resizeColumnElement:null,watch:{expandedKeys:function(t){this.d_expandedKeys=t},first:function(t){this.d_first=t},rows:function(t){this.d_rows=t},sortField:function(t){this.d_sortField=t},sortOrder:function(t){this.d_sortOrder=t},multiSortMeta:function(t){this.d_multiSortMeta=t}},beforeUnmount:function(){this.destroyStyleElement(),this.d_columns.clear()},methods:{columnProp:function(t,n){return de(t,n)},ptHeaderCellOptions:function(t){return{context:{frozen:this.columnProp(t,"frozen")}}},onNodeToggle:function(t){var n=this.nodeKey(t);this.d_expandedKeys[n]?(delete this.d_expandedKeys[n],this.$emit("node-collapse",t)):(this.d_expandedKeys[n]=!0,this.$emit("node-expand",t)),this.d_expandedKeys=W({},this.d_expandedKeys),this.$emit("update:expandedKeys",this.d_expandedKeys)},onNodeClick:function(t){if(this.rowSelectionMode&&t.node.selectable!==!1){var n=t.nodeTouched?!1:this.metaKeySelection,o=n?this.handleSelectionWithMetaKey(t):this.handleSelectionWithoutMetaKey(t);this.$emit("update:selectionKeys",o)}},nodeKey:function(t){return G(t,this.dataKey)},handleSelectionWithMetaKey:function(t){var n=t.originalEvent,o=t.node,r=this.nodeKey(o),l=n.metaKey||n.ctrlKey,s=this.isNodeSelected(o),d;return s&&l?(this.isSingleSelectionMode()?d={}:(d=W({},this.selectionKeys),delete d[r]),this.$emit("node-unselect",o)):(this.isSingleSelectionMode()?d={}:this.isMultipleSelectionMode()&&(d=l?this.selectionKeys?W({},this.selectionKeys):{}:{}),d[r]=!0,this.$emit("node-select",o)),d},handleSelectionWithoutMetaKey:function(t){var n=t.node,o=this.nodeKey(n),r=this.isNodeSelected(n),l;return this.isSingleSelectionMode()?r?(l={},this.$emit("node-unselect",n)):(l={},l[o]=!0,this.$emit("node-select",n)):r?(l=W({},this.selectionKeys),delete l[o],this.$emit("node-unselect",n)):(l=this.selectionKeys?W({},this.selectionKeys):{},l[o]=!0,this.$emit("node-select",n)),l},onCheckboxChange:function(t){this.$emit("update:selectionKeys",t.selectionKeys),t.check?this.$emit("node-select",t.node):this.$emit("node-unselect",t.node)},onRowRightClick:function(t){this.contextMenu&&($e(),t.originalEvent.target.focus()),this.$emit("update:contextMenuSelection",t.node),this.$emit("row-contextmenu",t)},isSingleSelectionMode:function(){return this.selectionMode==="single"},isMultipleSelectionMode:function(){return this.selectionMode==="multiple"},onPage:function(t){this.d_first=t.first,this.d_rows=t.rows;var n=this.createLazyLoadEvent(t);n.pageCount=t.pageCount,n.page=t.page,this.d_expandedKeys={},this.$emit("update:expandedKeys",this.d_expandedKeys),this.$emit("update:first",this.d_first),this.$emit("update:rows",this.d_rows),this.$emit("page",n)},resetPage:function(){this.d_first=0,this.$emit("update:first",this.d_first)},getFilterColumnHeaderClass:function(t){return[this.cx("headerCell",{column:t}),this.columnProp(t,"filterHeaderClass")]},onColumnHeaderClick:function(t){var n=t.originalEvent,o=t.column;if(this.columnProp(o,"sortable")){var r=n.target,l=this.columnProp(o,"sortField")||this.columnProp(o,"field");if(L(r,"data-p-sortable-column")===!0||L(r,"data-pc-section")==="columntitle"||L(r,"data-pc-section")==="columnheadercontent"||L(r,"data-pc-section")==="sorticon"||L(r.parentElement,"data-pc-section")==="sorticon"||L(r.parentElement.parentElement,"data-pc-section")==="sorticon"||r.closest('[data-p-sortable-column="true"]')){if($e(),this.sortMode==="single")this.d_sortField===l?this.removableSort&&this.d_sortOrder*-1===this.defaultSortOrder?(this.d_sortOrder=null,this.d_sortField=null):this.d_sortOrder=this.d_sortOrder*-1:(this.d_sortOrder=this.defaultSortOrder,this.d_sortField=l),this.$emit("update:sortField",this.d_sortField),this.$emit("update:sortOrder",this.d_sortOrder),this.resetPage();else if(this.sortMode==="multiple"){var s=n.metaKey||n.ctrlKey;s||(this.d_multiSortMeta=this.d_multiSortMeta.filter(function(d){return d.field===l})),this.addMultiSortField(l),this.$emit("update:multiSortMeta",this.d_multiSortMeta)}this.$emit("sort",this.createLazyLoadEvent(n))}}},addMultiSortField:function(t){var n=this.d_multiSortMeta.findIndex(function(o){return o.field===t});n>=0?this.removableSort&&this.d_multiSortMeta[n].order*-1===this.defaultSortOrder?this.d_multiSortMeta.splice(n,1):this.d_multiSortMeta[n]={field:t,order:this.d_multiSortMeta[n].order*-1}:this.d_multiSortMeta.push({field:t,order:this.defaultSortOrder}),this.d_multiSortMeta=ne(this.d_multiSortMeta)},sortSingle:function(t){return this.sortNodesSingle(t)},sortNodesSingle:function(t){var n=this,o=ne(t),r=Ae();return o.sort(function(l,s){var d=G(l.data,n.d_sortField),u=G(s.data,n.d_sortField);return Ue(d,u,n.d_sortOrder,r)}),o},sortMultiple:function(t){return this.sortNodesMultiple(t)},sortNodesMultiple:function(t){var n=this,o=ne(t);return o.sort(function(r,l){return n.multisortField(r,l,0)}),o},multisortField:function(t,n,o){var r=G(t.data,this.d_multiSortMeta[o].field),l=G(n.data,this.d_multiSortMeta[o].field),s=Ae();return r===l?this.d_multiSortMeta.length-1>o?this.multisortField(t,n,o+1):0:Ue(r,l,this.d_multiSortMeta[o].order,s)},filter:function(t){var n=[],o=this.filterMode==="strict",r=pe(t),l;try{for(r.s();!(l=r.n()).done;){for(var s=l.value,d=W({},s),u=!0,c=!1,i=0;i<this.columns.length;i++){var h=this.columns[i],C=this.columnProp(h,"filterField")||this.columnProp(h,"field");if(Object.prototype.hasOwnProperty.call(this.filters,C)){var F=this.columnProp(h,"filterMatchMode")||"startsWith",$=this.filters[C],N=De.filters[F],D={filterField:C,filterValue:$,filterConstraint:N,strict:o};if((o&&!(this.findFilteredNodes(d,D)||this.isFilterMatched(d,D))||!o&&!(this.isFilterMatched(d,D)||this.findFilteredNodes(d,D)))&&(u=!1),!u)break}if(this.hasGlobalFilter()&&!c){var V=W({},d),J=this.filters.global,I=De.filters.contains,A={filterField:C,filterValue:J,filterConstraint:I,strict:o};(o&&(this.findFilteredNodes(V,A)||this.isFilterMatched(V,A))||!o&&(this.isFilterMatched(V,A)||this.findFilteredNodes(V,A)))&&(c=!0,d=V)}}var ue=u;this.hasGlobalFilter()&&(ue=u&&c),ue&&n.push(d)}}catch(Ce){r.e(Ce)}finally{r.f()}var ce=this.createLazyLoadEvent(event);return ce.filteredValue=n,this.$emit("filter",ce),n},findFilteredNodes:function(t,n){if(t){var o=!1;if(t.children){var r=ne(t.children);t.children=[];var l=pe(r),s;try{for(l.s();!(s=l.n()).done;){var d=s.value,u=W({},d);this.isFilterMatched(u,n)&&(o=!0,t.children.push(u))}}catch(c){l.e(c)}finally{l.f()}}if(o)return!0}},isFilterMatched:function(t,n){var o=n.filterField,r=n.filterValue,l=n.filterConstraint,s=n.strict,d=!1,u=G(t.data,o);return l(u,r,this.filterLocale)&&(d=!0),(!d||s&&!this.isNodeLeaf(t))&&(d=this.findFilteredNodes(t,{filterField:o,filterValue:r,filterConstraint:l,strict:s})||d),d},isNodeSelected:function(t){return this.selectionMode&&this.selectionKeys?this.selectionKeys[this.nodeKey(t)]===!0:!1},isNodeLeaf:function(t){return t.leaf===!1?!1:!(t.children&&t.children.length)},createLazyLoadEvent:function(t){var n=this,o;return this.hasFilters()&&(o={},this.columns.forEach(function(r){n.columnProp(r,"field")&&(o[r.props.field]=n.columnProp(r,"filterMatchMode"))})),{originalEvent:t,first:this.d_first,rows:this.d_rows,sortField:this.d_sortField,sortOrder:this.d_sortOrder,multiSortMeta:this.d_multiSortMeta,filters:this.filters,filterMatchModes:o}},onColumnResizeStart:function(t){var n=Re(this.$el).left;this.resizeColumnElement=t.target.parentElement,this.columnResizing=!0,this.lastResizeHelperX=t.pageX-n+this.$el.scrollLeft,this.bindColumnResizeEvents()},onColumnResize:function(t){var n=Re(this.$el).left;this.$el.setAttribute("data-p-unselectable-text","true"),!this.isUnstyled&&Gt(this.$el,{"user-select":"none"}),this.$refs.resizeHelper.style.height=this.$el.offsetHeight+"px",this.$refs.resizeHelper.style.top="0px",this.$refs.resizeHelper.style.left=t.pageX-n+this.$el.scrollLeft+"px",this.$refs.resizeHelper.style.display="block"},onColumnResizeEnd:function(){var t=Wt(this.$el)?this.lastResizeHelperX-this.$refs.resizeHelper.offsetLeft:this.$refs.resizeHelper.offsetLeft-this.lastResizeHelperX,n=this.resizeColumnElement.offsetWidth,o=n+t,r=this.resizeColumnElement.style.minWidth||15;if(n+t>parseInt(r,10)){if(this.columnResizeMode==="fit"){var l=this.resizeColumnElement.nextElementSibling,s=l.offsetWidth-t;o>15&&s>15&&this.resizeTableCells(o,s)}else if(this.columnResizeMode==="expand"){var d=this.$refs.table.offsetWidth+t+"px",u=function(i){i&&(i.style.width=i.style.minWidth=d)};this.resizeTableCells(o),u(this.$refs.table)}this.$emit("column-resize-end",{element:this.resizeColumnElement,delta:t})}this.$refs.resizeHelper.style.display="none",this.resizeColumn=null,this.$el.removeAttribute("data-p-unselectable-text"),!this.isUnstyled&&(this.$el.style["user-select"]=""),this.unbindColumnResizeEvents()},resizeTableCells:function(t,n){var o=ct(this.resizeColumnElement),r=[],l=he(this.$refs.table,'thead[data-pc-section="thead"] > tr > th');l.forEach(function(u){return r.push(Z(u))}),this.destroyStyleElement(),this.createStyleElement();var s="",d='[data-pc-name="treetable"]['.concat(this.$attrSelector,'] > [data-pc-section="tablecontainer"] > table[data-pc-section="table"]');r.forEach(function(u,c){var i=c===o?t:n&&c===o+1?n:u,h="width: ".concat(i,"px !important; max-width: ").concat(i,"px !important");s+=`
                    `.concat(d,' > thead[data-pc-section="thead"] > tr > th:nth-child(').concat(c+1,`),
                    `).concat(d,' > tbody[data-pc-section="tbody"] > tr > td:nth-child(').concat(c+1,`),
                    `).concat(d,' > tfoot[data-pc-section="tfoot"] > tr > td:nth-child(').concat(c+1,`) {
                        `).concat(h,`
                    }
                `)}),this.styleElement.innerHTML=s},bindColumnResizeEvents:function(){var t=this;this.documentColumnResizeListener||(this.documentColumnResizeListener=document.addEventListener("mousemove",function(n){t.columnResizing&&t.onColumnResize(n)})),this.documentColumnResizeEndListener||(this.documentColumnResizeEndListener=document.addEventListener("mouseup",function(){t.columnResizing&&(t.columnResizing=!1,t.onColumnResizeEnd())}))},unbindColumnResizeEvents:function(){this.documentColumnResizeListener&&(document.removeEventListener("document",this.documentColumnResizeListener),this.documentColumnResizeListener=null),this.documentColumnResizeEndListener&&(document.removeEventListener("document",this.documentColumnResizeEndListener),this.documentColumnResizeEndListener=null)},onColumnKeyDown:function(t,n){(t.code==="Enter"||t.code==="NumpadEnter")&&t.currentTarget.nodeName==="TH"&&L(t.currentTarget,"data-p-sortable-column")&&this.onColumnHeaderClick(t,n)},hasColumnFilter:function(){if(this.columns){var t=pe(this.columns),n;try{for(t.s();!(n=t.n()).done;){var o=n.value;if(o.children&&o.children.filter)return!0}}catch(r){t.e(r)}finally{t.f()}}return!1},hasFilters:function(){return this.filters&&Object.keys(this.filters).length>0&&this.filters.constructor===Object},hasGlobalFilter:function(){return this.filters&&Object.prototype.hasOwnProperty.call(this.filters,"global")},getItemLabel:function(t){return t.data.name},createStyleElement:function(){var t;this.styleElement=document.createElement("style"),this.styleElement.type="text/css",Ht(this.styleElement,"nonce",(t=this.$primevue)===null||t===void 0||(t=t.config)===null||t===void 0||(t=t.csp)===null||t===void 0?void 0:t.nonce),document.head.appendChild(this.styleElement)},destroyStyleElement:function(){this.styleElement&&(document.head.removeChild(this.styleElement),this.styleElement=null)},setTabindex:function(t,n){if(this.isNodeSelected(t))return this.hasASelectedNode=!0,0;if(this.selectionMode){if(!this.isNodeSelected(t)&&n===0&&!this.hasASelectedNode)return 0}else if(!this.selectionMode&&n===0)return 0;return-1}},computed:{columns:function(){return this.d_columns.get(this)},processedData:function(){if(this.lazy)return this.value;if(this.value&&this.value.length){var t=this.value;return this.sorted&&(this.sortMode==="single"?t=this.sortSingle(t):this.sortMode==="multiple"&&(t=this.sortMultiple(t))),this.hasFilters()&&(t=this.filter(t)),t}else return null},dataToRender:function(){var t=this.processedData;if(this.paginator){var n=this.lazy?0:this.d_first;return t.slice(n,n+this.d_rows)}else return t},empty:function(){var t=this.processedData;return!t||t.length===0},sorted:function(){return this.d_sortField||this.d_multiSortMeta&&this.d_multiSortMeta.length>0},hasFooter:function(){var t=!1,n=pe(this.columns),o;try{for(n.s();!(o=n.n()).done;){var r=o.value;if(this.columnProp(r,"footer")||r.children&&r.children.footer){t=!0;break}}}catch(l){n.e(l)}finally{n.f()}return t},paginatorTop:function(){return this.paginator&&(this.paginatorPosition!=="bottom"||this.paginatorPosition==="both")},paginatorBottom:function(){return this.paginator&&(this.paginatorPosition!=="top"||this.paginatorPosition==="both")},singleSelectionMode:function(){return this.selectionMode&&this.selectionMode==="single"},multipleSelectionMode:function(){return this.selectionMode&&this.selectionMode==="multiple"},rowSelectionMode:function(){return this.singleSelectionMode||this.multipleSelectionMode},totalRecordsLength:function(){if(this.lazy)return this.totalRecords;var t=this.processedData;return t?t.length:0}},components:{TTRow:Ct,TTPaginator:Rt,TTHeaderCell:gt,TTFooterCell:bt,SpinnerIcon:it}};function se(e){"@babel/helpers - typeof";return se=typeof Symbol=="function"&&typeof Symbol.iterator=="symbol"?function(t){return typeof t}:function(t){return t&&typeof Symbol=="function"&&t.constructor===Symbol&&t!==Symbol.prototype?"symbol":typeof t},se(e)}function nt(e,t){var n=Object.keys(e);if(Object.getOwnPropertySymbols){var o=Object.getOwnPropertySymbols(e);t&&(o=o.filter(function(r){return Object.getOwnPropertyDescriptor(e,r).enumerable})),n.push.apply(n,o)}return n}function lt(e){for(var t=1;t<arguments.length;t++){var n=arguments[t]!=null?arguments[t]:{};t%2?nt(Object(n),!0).forEach(function(o){$n(e,o,n[o])}):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(n)):nt(Object(n)).forEach(function(o){Object.defineProperty(e,o,Object.getOwnPropertyDescriptor(n,o))})}return e}function $n(e,t,n){return(t=Vn(t))in e?Object.defineProperty(e,t,{value:n,enumerable:!0,configurable:!0,writable:!0}):e[t]=n,e}function Vn(e){var t=Nn(e,"string");return se(t)=="symbol"?t:t+""}function Nn(e,t){if(se(e)!="object"||!e)return e;var n=e[Symbol.toPrimitive];if(n!==void 0){var o=n.call(e,t);if(se(o)!="object")return o;throw new TypeError("@@toPrimitive must return a primitive value.")}return(t==="string"?String:Number)(e)}var Hn=["colspan"];function Wn(e,t,n,o,r,l){var s=E("TTPaginator"),d=E("TTHeaderCell"),u=E("TTRow"),c=E("TTFooterCell");return a(),m("div",f({class:e.cx("root"),"data-scrollselectors":".p-treetable-scrollable-body"},e.ptmi("root")),[k(e.$slots,"default"),e.loading&&e.loadingMode==="mask"?(a(),m("div",f({key:0,class:e.cx("loading")},e.ptm("loading")),[v("div",f({class:e.cx("mask")},e.ptm("mask")),[k(e.$slots,"loadingicon",{class:M(e.cx("loadingIcon"))},function(){return[(a(),g(x(e.loadingIcon?"span":"SpinnerIcon"),f({spin:"",class:[e.cx("loadingIcon"),e.loadingIcon]},e.ptm("loadingIcon")),null,16,["class"]))]})],16)],16)):w("",!0),e.$slots.header?(a(),m("div",f({key:1,class:e.cx("header")},e.ptm("header")),[k(e.$slots,"header")],16)):w("",!0),l.paginatorTop?(a(),g(s,{key:2,rows:r.d_rows,first:r.d_first,totalRecords:l.totalRecordsLength,pageLinkSize:e.pageLinkSize,template:e.paginatorTemplate,rowsPerPageOptions:e.rowsPerPageOptions,currentPageReportTemplate:e.currentPageReportTemplate,class:M(e.cx("pcPaginator",{position:"top"})),onPage:t[0]||(t[0]=function(i){return l.onPage(i)}),alwaysShow:e.alwaysShowPaginator,unstyled:e.unstyled,pt:e.ptm("pcPaginator")},He({_:2},[e.$slots.paginatorcontainer?{name:"container",fn:y(function(i){return[k(e.$slots,"paginatorcontainer",{first:i.first,last:i.last,rows:i.rows,page:i.page,pageCount:i.pageCount,totalRecords:i.totalRecords,firstPageCallback:i.firstPageCallback,lastPageCallback:i.lastPageCallback,prevPageCallback:i.prevPageCallback,nextPageCallback:i.nextPageCallback,rowChangeCallback:i.rowChangeCallback})]}),key:"0"}:void 0,e.$slots.paginatorstart?{name:"start",fn:y(function(){return[k(e.$slots,"paginatorstart")]}),key:"1"}:void 0,e.$slots.paginatorend?{name:"end",fn:y(function(){return[k(e.$slots,"paginatorend")]}),key:"2"}:void 0,e.$slots.paginatorfirstpagelinkicon?{name:"firstpagelinkicon",fn:y(function(i){return[k(e.$slots,"paginatorfirstpagelinkicon",{class:M(i.class)})]}),key:"3"}:void 0,e.$slots.paginatorprevpagelinkicon?{name:"prevpagelinkicon",fn:y(function(i){return[k(e.$slots,"paginatorprevpagelinkicon",{class:M(i.class)})]}),key:"4"}:void 0,e.$slots.paginatornextpagelinkicon?{name:"nextpagelinkicon",fn:y(function(i){return[k(e.$slots,"paginatornextpagelinkicon",{class:M(i.class)})]}),key:"5"}:void 0,e.$slots.paginatorlastpagelinkicon?{name:"lastpagelinkicon",fn:y(function(i){return[k(e.$slots,"paginatorlastpagelinkicon",{class:M(i.class)})]}),key:"6"}:void 0,e.$slots.paginatorjumptopagedropdownicon?{name:"jumptopagedropdownicon",fn:y(function(i){return[k(e.$slots,"paginatorjumptopagedropdownicon",{class:M(i.class)})]}),key:"7"}:void 0,e.$slots.paginatorrowsperpagedropdownicon?{name:"rowsperpagedropdownicon",fn:y(function(i){return[k(e.$slots,"paginatorrowsperpagedropdownicon",{class:M(i.class)})]}),key:"8"}:void 0]),1032,["rows","first","totalRecords","pageLinkSize","template","rowsPerPageOptions","currentPageReportTemplate","class","alwaysShow","unstyled","pt"])):w("",!0),v("div",f({class:e.cx("tableContainer"),style:[e.sx("tableContainer"),{maxHeight:e.scrollHeight}]},e.ptm("tableContainer")),[v("table",f({ref:"table",role:"treegrid",class:[e.cx("table"),e.tableClass],style:e.tableStyle},lt(lt({},e.tableProps),e.ptm("table"))),[v("thead",f({class:e.cx("thead"),style:e.sx("thead"),role:"rowgroup"},e.ptm("thead")),[v("tr",f({role:"row"},e.ptm("headerRow")),[(a(!0),m(O,null,U(l.columns,function(i,h){return a(),m(O,{key:l.columnProp(i,"columnKey")||l.columnProp(i,"field")||h},[l.columnProp(i,"hidden")?w("",!0):(a(),g(d,{key:0,column:i,resizableColumns:e.resizableColumns,sortField:r.d_sortField,sortOrder:r.d_sortOrder,multiSortMeta:r.d_multiSortMeta,sortMode:e.sortMode,onColumnClick:t[1]||(t[1]=function(C){return l.onColumnHeaderClick(C)}),onColumnResizestart:t[2]||(t[2]=function(C){return l.onColumnResizeStart(C)}),index:h,unstyled:e.unstyled,pt:e.pt},null,8,["column","resizableColumns","sortField","sortOrder","multiSortMeta","sortMode","index","unstyled","pt"]))],64)}),128))],16),l.hasColumnFilter()?(a(),m("tr",Ke(f({key:0},e.ptm("headerRow"))),[(a(!0),m(O,null,U(l.columns,function(i,h){return a(),m(O,{key:l.columnProp(i,"columnKey")||l.columnProp(i,"field")||h},[l.columnProp(i,"hidden")?w("",!0):(a(),m("th",f({key:0,class:l.getFilterColumnHeaderClass(i),style:[l.columnProp(i,"style"),l.columnProp(i,"filterHeaderStyle")]},{ref_for:!0},e.ptm("headerCell",l.ptHeaderCellOptions(i))),[i.children&&i.children.filter?(a(),g(x(i.children.filter),{key:0,column:i,index:h},null,8,["column","index"])):w("",!0)],16))],64)}),128))],16)):w("",!0)],16),v("tbody",f({class:e.cx("tbody"),role:"rowgroup"},e.ptm("tbody")),[l.empty?(a(),m("tr",f({key:1,class:e.cx("emptyMessage")},e.ptm("emptyMessage")),[v("td",f({colspan:l.columns.length},e.ptm("emptyMessageCell")),[k(e.$slots,"empty")],16,Hn)],16)):(a(!0),m(O,{key:0},U(l.dataToRender,function(i,h){return a(),g(u,{key:l.nodeKey(i),dataKey:e.dataKey,columns:l.columns,node:i,level:0,expandedKeys:r.d_expandedKeys,indentation:e.indentation,selectionMode:e.selectionMode,selectionKeys:e.selectionKeys,ariaSetSize:l.dataToRender.length,ariaPosInset:h+1,tabindex:l.setTabindex(i,h),loadingMode:e.loadingMode,contextMenu:e.contextMenu,contextMenuSelection:e.contextMenuSelection,templates:e.$slots,onNodeToggle:l.onNodeToggle,onNodeClick:l.onNodeClick,onCheckboxChange:l.onCheckboxChange,onRowRightclick:t[3]||(t[3]=function(C){return l.onRowRightClick(C)}),unstyled:e.unstyled,pt:e.pt},null,8,["dataKey","columns","node","expandedKeys","indentation","selectionMode","selectionKeys","ariaSetSize","ariaPosInset","tabindex","loadingMode","contextMenu","contextMenuSelection","templates","onNodeToggle","onNodeClick","onCheckboxChange","unstyled","pt"])}),128))],16),l.hasFooter?(a(),m("tfoot",f({key:0,class:e.cx("tfoot"),style:e.sx("tfoot"),role:"rowgroup"},e.ptm("tfoot")),[v("tr",f({role:"row"},e.ptm("footerRow")),[(a(!0),m(O,null,U(l.columns,function(i,h){return a(),m(O,{key:l.columnProp(i,"columnKey")||l.columnProp(i,"field")||h},[l.columnProp(i,"hidden")?w("",!0):(a(),g(c,{key:0,column:i,index:h,unstyled:e.unstyled,pt:e.pt},null,8,["column","index","unstyled","pt"]))],64)}),128))],16)],16)):w("",!0)],16)],16),l.paginatorBottom?(a(),g(s,{key:3,rows:r.d_rows,first:r.d_first,totalRecords:l.totalRecordsLength,pageLinkSize:e.pageLinkSize,template:e.paginatorTemplate,rowsPerPageOptions:e.rowsPerPageOptions,currentPageReportTemplate:e.currentPageReportTemplate,class:M(e.cx("pcPaginator",{position:"bottom"})),onPage:t[4]||(t[4]=function(i){return l.onPage(i)}),alwaysShow:e.alwaysShowPaginator,unstyled:e.unstyled,pt:e.ptm("pcPaginator")},He({_:2},[e.$slots.paginatorcontainer?{name:"container",fn:y(function(i){return[k(e.$slots,"paginatorcontainer",{first:i.first,last:i.last,rows:i.rows,page:i.page,pageCount:i.pageCount,totalRecords:i.totalRecords,firstPageCallback:i.firstPageCallback,lastPageCallback:i.lastPageCallback,prevPageCallback:i.prevPageCallback,nextPageCallback:i.nextPageCallback,rowChangeCallback:i.rowChangeCallback})]}),key:"0"}:void 0,e.$slots.paginatorstart?{name:"start",fn:y(function(){return[k(e.$slots,"paginatorstart")]}),key:"1"}:void 0,e.$slots.paginatorend?{name:"end",fn:y(function(){return[k(e.$slots,"paginatorend")]}),key:"2"}:void 0,e.$slots.paginatorfirstpagelinkicon?{name:"firstpagelinkicon",fn:y(function(i){return[k(e.$slots,"paginatorfirstpagelinkicon",{class:M(i.class)})]}),key:"3"}:void 0,e.$slots.paginatorprevpagelinkicon?{name:"prevpagelinkicon",fn:y(function(i){return[k(e.$slots,"paginatorprevpagelinkicon",{class:M(i.class)})]}),key:"4"}:void 0,e.$slots.paginatornextpagelinkicon?{name:"nextpagelinkicon",fn:y(function(i){return[k(e.$slots,"paginatornextpagelinkicon",{class:M(i.class)})]}),key:"5"}:void 0,e.$slots.paginatorlastpagelinkicon?{name:"lastpagelinkicon",fn:y(function(i){return[k(e.$slots,"paginatorlastpagelinkicon",{class:M(i.class)})]}),key:"6"}:void 0,e.$slots.paginatorjumptopagedropdownicon?{name:"jumptopagedropdownicon",fn:y(function(i){return[k(e.$slots,"paginatorjumptopagedropdownicon",{class:M(i.class)})]}),key:"7"}:void 0,e.$slots.paginatorrowsperpagedropdownicon?{name:"rowsperpagedropdownicon",fn:y(function(i){return[k(e.$slots,"paginatorrowsperpagedropdownicon",{class:M(i.class)})]}),key:"8"}:void 0]),1032,["rows","first","totalRecords","pageLinkSize","template","rowsPerPageOptions","currentPageReportTemplate","class","alwaysShow","unstyled","pt"])):w("",!0),e.$slots.footer?(a(),m("div",f({key:4,class:e.cx("footer")},e.ptm("footer")),[k(e.$slots,"footer")],16)):w("",!0),v("div",f({ref:"resizeHelper",class:e.cx("columnResizeIndicator"),style:{display:"none"}},e.ptm("columnResizeIndicator")),null,16)],16)}wt.render=Wn;const Gn={key:1},Yn={class:"flex"},Xn=Y({__name:"BaseTree",props:Q({columns:{default:()=>[]},title:{default:""},detailComponent:{},initialValues:{},baseUrl:{},client:{type:[Function,Object]}},{modelValue:{default:()=>[]},modelModifiers:{}}),emits:["update:modelValue"],setup(e){const{pushComponent:t}=ht(),n=ee(e,"modelValue");function o(p,b){const P=h(p).order??0,z=h(b).order??0;return P-z}const r=ve(()=>{const p=[...n.value];return p.sort(o),p.map(b=>D(b,0))}),l=R({}),s=R(null),d=R({}),u=R({}),c=R({});function i(p){return!!p.pivot&&typeof p.pivot=="object"}function h(p){return p.pivot??{}}function C(p){p.pivot?typeof p.pivot.order!="number"&&(p.pivot.order=0):p.pivot={order:0,key:""}}function F(p){return typeof p.order=="number"?p.order:0}function $(p,b){p.order=Number(b??0)}function N(p,b){const P=F(p),z=F(b);return P-z}function D(p,b=0){b===0&&!i(p)&&C(p);const z=[...Array.isArray(p.children)?p.children:[]].sort(N);return{key:p.id,data:p,children:z.length?z.map(B=>D(B,b+1)):void 0}}function V(p,b,P){for(let z=0;z<p.length;z++){const B=p[z];if(B.id===b.id)return p.splice(z,1,b),!0;const T=Array.isArray(B.children)?B.children:[];if(P&&B.id===P)return B.children=[...T,b],!0;if(T.length&&V(T,b,P))return B.children=T,!0}return!1}function J(p,b){for(let P=0;P<p.length;P++){const z=p[P];if(z.id===b)return p.splice(P,1),!0;const B=Array.isArray(z.children)?z.children:[];if(B.length&&J(B,b))return z.children=B,!0}return!1}const I=p=>{const b=[...n.value],P=s.value?s.value.id:null,z=p.parent_id,T=V(b,p,typeof z=="string"?z:P);n.value=T?b:[...b,p]},A=p=>{const b=[...n.value],P=J(b,p.id);n.value=P?b:b.filter(z=>z.id!==p.id)};async function ue(p,b){if(!(!e.client||!e.baseUrl))try{await e.client.put(`${e.baseUrl}/${p}`,{order:F(b)}),c.value[p]=!1,delete u.value[p]}catch{const z=u.value[p];typeof z=="number"&&$(b,z),c.value[p]=!1,delete u.value[p]}}function ce(p,b){return ue(p,b)}function Ce(p){return pt.debounce(ce.bind(null,p),300)}function Ot(p){const b=d.value;return b[p]||(b[p]=Ce(p)),b[p]}const xt=p=>{if(!e.client||!e.baseUrl)return;const b=p.id;if(!b)return;Ot(b)(p)},It=(p,b)=>{const P=p.id;P&&(c.value[P]===!0||(u.value[P]=F(p),c.value[P]=!0)),$(p,b),xt(p)};function Kt(p,b){const P=Number(b);It(p,P)}const Bt=({data:p})=>{s.value=p,t(e.detailComponent.component,{id:p.id,initialValues:e.initialValues,...e.detailComponent.props,onSave:I,onDelete:A,modal:!0})},Et=()=>{const p={...e.initialValues||{},...s.value?{parent_id:s.value.id}:{}};t(e.detailComponent.component,{initialValues:p,...e.detailComponent.props,onSave:I,onDelete:A,modal:!0})};return Jt(()=>{const p=d.value;Object.keys(p).forEach(b=>{p[b].cancel()})}),(p,b)=>{const P=ge,z=Oe,B=_;return a(),m("div",null,[v("h3",null,j(p.title),1),S(K(wt),{value:r.value,size:"small",showGridlines:"",stripedRows:"",selectionMode:"checkbox",selectionKeys:l.value,"onUpdate:selectionKeys":b[0]||(b[0]=T=>l.value=T),metaKeySelection:!1},{footer:y(()=>[v("div",Yn,[b[1]||(b[1]=v("div",{class:"flex-1"},null,-1)),S(B,{type:"button",icon:"pi pi-plus",text:"",onClick:Et})])]),default:y(()=>[S(K(q),{expander:"",style:{width:"3rem"}}),(a(!0),m(O,null,U(p.columns,T=>(a(),g(K(q),{key:T.field,field:T.field,header:T.header},null,8,["field","header"]))),128)),S(K(q),{header:"Ключ",style:{"min-width":"10rem"}},{body:y(({node:T})=>[i(T.data)?(a(),g(P,{key:0,modelValue:h(T.data).key,"onUpdate:modelValue":X=>h(T.data).key=X,placeholder:"key",size:"small"},null,8,["modelValue","onUpdate:modelValue"])):(a(),m("span",Gn," "))]),_:1}),S(K(q),{header:"Порядок",style:{width:"8rem"}},{body:y(({node:T})=>[i(T.data)?(a(),g(z,{key:0,modelValue:h(T.data).order,"onUpdate:modelValue":X=>h(T.data).order=X,min:0,step:1,showButtons:"",buttonLayout:"horizontal",incrementButtonClass:"p-button-text",decrementButtonClass:"p-button-text"},null,8,["modelValue","onUpdate:modelValue"])):(a(),g(z,{key:1,modelValue:F(T.data),"onUpdate:modelValue":X=>Kt(T.data,X),min:0,step:1,showButtons:"",buttonLayout:"horizontal",incrementButtonClass:"p-button-text",decrementButtonClass:"p-button-text"},null,8,["modelValue","onUpdate:modelValue"]))]),_:1}),S(K(q),{header:"Действия",style:{width:"4rem"}},{body:y(({node:T})=>[S(B,{icon:"pi pi-pencil",text:"",onClick:X=>Bt({data:T.data})},null,8,["onClick"])]),_:1})]),_:1},8,["value","selectionKeys"])])}}}),qn={class:"text-lg font-medium"},Zn={class:"flex gap-4 mt-1"},St=Y({__name:"BaseDetail",props:Q({fields:{default:()=>[]},title:{default:"Создание"},entity:{},client:{type:[Function,Object]},baseUrl:{},initialValues:{},id:{},relations:{default:()=>[]},modal:{type:Boolean,default:!1}},{form:{},formModifiers:{}}),emits:Q(["save","delete","close"],["update:form"]),setup(e,{emit:t}){const n=t,o=ft(),r=ee(e,"form"),l=R(!1),s=async()=>{l.value=!0;const{data:C}=await e.client.post(e.baseUrl,r.value?.values,{params:{relations:e.relations}});l.value=!1,e.modal?n("save",C):o.push({name:`${pt.capitalize(e.entity)}Detail`,params:{id:C.id}})},d=async()=>{l.value=!0;const{data:C}=await e.client.put(`${e.baseUrl}/${e.id}`,r.value?.values,{params:{relations:e.relations}});r.value?.setValues(C),e.modal&&n("save",C),l.value=!1},{handler:u}=nn(e.id?d:s,r),c=async()=>{const{data:C}=await e.client.get(`${e.baseUrl}/${e.id}`,{params:{relations:e.relations}});r.value?.setValues(C)},i=()=>{e.modal?n("close"):o.back()};_t(()=>{e.id&&c()});const h=async()=>{e.id&&(l.value=!0,await e.client.delete(`${e.baseUrl}/${e.id}`),l.value=!1,n("delete",{id:e.id}),i())};return(C,F)=>{const $=_,N=tn;return a(),g(N,{class:"flex flex-col h-full",pt:{body:{class:"flex-1"},content:{class:"flex-1"}}},{title:y(()=>[k(C.$slots,"title",{},()=>[v("h3",qn,j(C.title),1)])]),content:y(()=>[S(ln,{class:"flex-1",fields:C.fields,form:r.value,"onUpdate:form":F[0]||(F[0]=D=>r.value=D),initialValues:C.initialValues},null,8,["fields","form","initialValues"])]),footer:y(()=>[k(C.$slots,"footer",{},()=>[v("div",Zn,[F[1]||(F[1]=v("div",{class:"flex-1"},null,-1)),S($,{label:"Cancel",severity:"secondary",outlined:"",onClick:i}),C.id?(a(),g($,{key:0,label:"Delete",severity:"danger",outlined:"",onClick:h})):w("",!0),S($,{label:"Save",onClick:K(u)},null,8,["onClick"])])])]),_:3})}}}),Qn=Y({__name:"CategoryDetail",props:{id:{}},setup(e){const{fields:t}=Ll({id:e.id}),n=R();return(o,r)=>(a(),g(St,f({baseUrl:K(en),client:K(me),entity:"news",fields:K(t),form:n.value,"onUpdate:form":r[0]||(r[0]=l=>n.value=l)},o.$attrs,{relations:["children","dishes"],id:o.id}),null,16,["baseUrl","client","fields","form","id"]))}}),Jn={class:"flex justify-between items-center mb-3"},_n={class:"flex"},el=Y({__name:"BaseRelationTable",props:Q({title:{},columns:{},entity:{},detailComponent:{},initialValues:{}},{modelValue:{default:()=>[]},modelModifiers:{}}),emits:["update:modelValue"],setup(e){ft();const{pushComponent:t}=ht(),n=ee(e,"modelValue"),o=R([]),r=u=>{const c=n.value.findIndex(h=>h.id===u.id);if(c>=0){const h=[...n.value];h.splice(c,1,u),n.value=h}else n.value=[...n.value,u];const i=e.detailComponent.props&&e.detailComponent.props.onSave;i&&i(u)},l=u=>{const c=n.value.filter(h=>h.id!==u.id);n.value=c;const i=e.detailComponent.props&&e.detailComponent.props.onDelete;i&&i(u)},s=({data:u})=>{t(e.detailComponent.component,{id:u.id,initialValues:e.initialValues,...e.detailComponent.props,onSave:r,onDelete:l})},d=()=>{t(e.detailComponent.component,{initialValues:e.initialValues,...e.detailComponent.props,onSave:r,onDelete:l})};return(u,c)=>{const i=q,h=_,C=ut;return a(),m("div",null,[v("div",Jn,[v("h3",null,j(u.title),1)]),S(C,{value:n.value,showGridlines:"",stripedRows:"",onRowClick:s,selection:o.value,"onUpdate:selection":c[0]||(c[0]=F=>o.value=F),size:"small"},{footer:y(()=>[v("div",_n,[c[1]||(c[1]=v("div",{class:"flex-1"},null,-1)),S(h,{type:"button",size:"small",icon:"pi pi-plus",text:"",onClick:d})])]),default:y(()=>[S(i,{selectionMode:"multiple"}),(a(!0),m(O,null,U(u.columns,F=>k(u.$slots,`column-${F.field}`,{},()=>[(a(),g(i,{key:F.field,field:F.field,header:F.header,class:"cursor-pointer"},null,8,["field","header"]))])),256))]),_:3},8,["value","selection"])])}}}),tl="/api/account/dishes";var nl=`
    .p-floatlabel {
        display: block;
        position: relative;
    }

    .p-floatlabel label {
        position: absolute;
        pointer-events: none;
        top: 50%;
        transform: translateY(-50%);
        transition-property: all;
        transition-timing-function: ease;
        line-height: 1;
        font-weight: dt('floatlabel.font.weight');
        inset-inline-start: dt('floatlabel.position.x');
        color: dt('floatlabel.color');
        transition-duration: dt('floatlabel.transition.duration');
    }

    .p-floatlabel:has(.p-textarea) label {
        top: dt('floatlabel.position.y');
        transform: translateY(0);
    }

    .p-floatlabel:has(.p-inputicon:first-child) label {
        inset-inline-start: calc((dt('form.field.padding.x') * 2) + dt('icon.size'));
    }

    .p-floatlabel:has(input:focus) label,
    .p-floatlabel:has(input.p-filled) label,
    .p-floatlabel:has(input:-webkit-autofill) label,
    .p-floatlabel:has(textarea:focus) label,
    .p-floatlabel:has(textarea.p-filled) label,
    .p-floatlabel:has(.p-inputwrapper-focus) label,
    .p-floatlabel:has(.p-inputwrapper-filled) label,
    .p-floatlabel:has(input[placeholder]) label,
    .p-floatlabel:has(textarea[placeholder]) label {
        top: dt('floatlabel.over.active.top');
        transform: translateY(0);
        font-size: dt('floatlabel.active.font.size');
        font-weight: dt('floatlabel.active.font.weight');
    }

    .p-floatlabel:has(input.p-filled) label,
    .p-floatlabel:has(textarea.p-filled) label,
    .p-floatlabel:has(.p-inputwrapper-filled) label {
        color: dt('floatlabel.active.color');
    }

    .p-floatlabel:has(input:focus) label,
    .p-floatlabel:has(input:-webkit-autofill) label,
    .p-floatlabel:has(textarea:focus) label,
    .p-floatlabel:has(.p-inputwrapper-focus) label {
        color: dt('floatlabel.focus.color');
    }

    .p-floatlabel-in .p-inputtext,
    .p-floatlabel-in .p-textarea,
    .p-floatlabel-in .p-select-label,
    .p-floatlabel-in .p-multiselect-label,
    .p-floatlabel-in .p-autocomplete-input-multiple,
    .p-floatlabel-in .p-cascadeselect-label,
    .p-floatlabel-in .p-treeselect-label {
        padding-block-start: dt('floatlabel.in.input.padding.top');
        padding-block-end: dt('floatlabel.in.input.padding.bottom');
    }

    .p-floatlabel-in:has(input:focus) label,
    .p-floatlabel-in:has(input.p-filled) label,
    .p-floatlabel-in:has(input:-webkit-autofill) label,
    .p-floatlabel-in:has(textarea:focus) label,
    .p-floatlabel-in:has(textarea.p-filled) label,
    .p-floatlabel-in:has(.p-inputwrapper-focus) label,
    .p-floatlabel-in:has(.p-inputwrapper-filled) label,
    .p-floatlabel-in:has(input[placeholder]) label,
    .p-floatlabel-in:has(textarea[placeholder]) label {
        top: dt('floatlabel.in.active.top');
    }

    .p-floatlabel-on:has(input:focus) label,
    .p-floatlabel-on:has(input.p-filled) label,
    .p-floatlabel-on:has(input:-webkit-autofill) label,
    .p-floatlabel-on:has(textarea:focus) label,
    .p-floatlabel-on:has(textarea.p-filled) label,
    .p-floatlabel-on:has(.p-inputwrapper-focus) label,
    .p-floatlabel-on:has(.p-inputwrapper-filled) label,
    .p-floatlabel-on:has(input[placeholder]) label,
    .p-floatlabel-on:has(textarea[placeholder]) label {
        top: 0;
        transform: translateY(-50%);
        border-radius: dt('floatlabel.on.border.radius');
        background: dt('floatlabel.on.active.background');
        padding: dt('floatlabel.on.active.padding');
    }

    .p-floatlabel:has([class^='p-'][class$='-fluid']) {
        width: 100%;
    }

    .p-floatlabel:has(.p-invalid) label {
        color: dt('floatlabel.invalid.color');
    }
`,ll={root:function(t){var n=t.props;return["p-floatlabel",{"p-floatlabel-over":n.variant==="over","p-floatlabel-on":n.variant==="on","p-floatlabel-in":n.variant==="in"}]}},ol=ye.extend({name:"floatlabel",style:nl,classes:ll}),rl={name:"BaseFloatLabel",extends:H,props:{variant:{type:String,default:"over"}},style:ol,provide:function(){return{$pcFloatLabel:this,$parentInstance:this}}},Be={name:"FloatLabel",extends:rl,inheritAttrs:!1};function il(e,t,n,o,r,l){return a(),m("span",f({class:e.cx("root")},e.ptmi("root")),[k(e.$slots,"default")],16)}Be.render=il;const al={for:"on_label"},Pe=Y({__name:"BaseInput",props:Q({label:{}},{modelValue:{default:""},modelModifiers:{}}),emits:["update:modelValue"],setup(e){const t=ee(e,"modelValue");return(n,o)=>(a(),g(K(Be),{variant:"on"},{default:y(()=>[S(K(ge),f({id:"on_label",class:"w-full",modelValue:t.value,"onUpdate:modelValue":o[0]||(o[0]=r=>t.value=r)},n.$attrs),null,16,["modelValue"]),v("label",al,j(n.label),1)]),_:1}))}}),sl={for:"on_label"},ot=Y({__name:"BaseNumberInput",props:Q({label:{}},{modelValue:{default:0},modelModifiers:{}}),emits:["update:modelValue"],setup(e){const t=ee(e,"modelValue");return(n,o)=>(a(),g(K(Be),{variant:"on"},{default:y(()=>[S(K(Oe),f({id:"on_label",class:"w-full",modelValue:t.value,"onUpdate:modelValue":o[0]||(o[0]=r=>t.value=r)},n.$attrs),null,16,["modelValue"]),v("label",sl,j(n.label),1)]),_:1}))}});var Pt={name:"UploadIcon",extends:jt};function dl(e,t,n,o,r,l){return a(),m("svg",f({width:"14",height:"14",viewBox:"0 0 14 14",fill:"none",xmlns:"http://www.w3.org/2000/svg"},e.pti()),t[0]||(t[0]=[v("path",{"fill-rule":"evenodd","clip-rule":"evenodd",d:"M6.58942 9.82197C6.70165 9.93405 6.85328 9.99793 7.012 10C7.17071 9.99793 7.32234 9.93405 7.43458 9.82197C7.54681 9.7099 7.61079 9.55849 7.61286 9.4V2.04798L9.79204 4.22402C9.84752 4.28011 9.91365 4.32457 9.98657 4.35479C10.0595 4.38502 10.1377 4.40039 10.2167 4.40002C10.2956 4.40039 10.3738 4.38502 10.4467 4.35479C10.5197 4.32457 10.5858 4.28011 10.6413 4.22402C10.7538 4.11152 10.817 3.95902 10.817 3.80002C10.817 3.64102 10.7538 3.48852 10.6413 3.37602L7.45127 0.190618C7.44656 0.185584 7.44176 0.180622 7.43687 0.175736C7.32419 0.063214 7.17136 0 7.012 0C6.85264 0 6.69981 0.063214 6.58712 0.175736C6.58181 0.181045 6.5766 0.186443 6.5715 0.191927L3.38282 3.37602C3.27669 3.48976 3.2189 3.6402 3.22165 3.79564C3.2244 3.95108 3.28746 4.09939 3.39755 4.20932C3.50764 4.31925 3.65616 4.38222 3.81182 4.38496C3.96749 4.3877 4.11814 4.33001 4.23204 4.22402L6.41113 2.04807V9.4C6.41321 9.55849 6.47718 9.7099 6.58942 9.82197ZM11.9952 14H2.02883C1.751 13.9887 1.47813 13.9228 1.22584 13.8061C0.973545 13.6894 0.746779 13.5241 0.558517 13.3197C0.370254 13.1154 0.22419 12.876 0.128681 12.6152C0.0331723 12.3545 -0.00990605 12.0775 0.0019109 11.8V9.40005C0.0019109 9.24092 0.065216 9.08831 0.1779 8.97579C0.290584 8.86326 0.443416 8.80005 0.602775 8.80005C0.762134 8.80005 0.914966 8.86326 1.02765 8.97579C1.14033 9.08831 1.20364 9.24092 1.20364 9.40005V11.8C1.18295 12.0376 1.25463 12.274 1.40379 12.4602C1.55296 12.6463 1.76817 12.7681 2.00479 12.8H11.9952C12.2318 12.7681 12.447 12.6463 12.5962 12.4602C12.7453 12.274 12.817 12.0376 12.7963 11.8V9.40005C12.7963 9.24092 12.8596 9.08831 12.9723 8.97579C13.085 8.86326 13.2378 8.80005 13.3972 8.80005C13.5565 8.80005 13.7094 8.86326 13.8221 8.97579C13.9347 9.08831 13.998 9.24092 13.998 9.40005V11.8C14.022 12.3563 13.8251 12.8996 13.45 13.3116C13.0749 13.7236 12.552 13.971 11.9952 14Z",fill:"currentColor"},null,-1)]),16)}Pt.render=dl;var ul=`
    .p-progressbar {
        display: block;
        position: relative;
        overflow: hidden;
        height: dt('progressbar.height');
        background: dt('progressbar.background');
        border-radius: dt('progressbar.border.radius');
    }

    .p-progressbar-value {
        margin: 0;
        background: dt('progressbar.value.background');
    }

    .p-progressbar-label {
        color: dt('progressbar.label.color');
        font-size: dt('progressbar.label.font.size');
        font-weight: dt('progressbar.label.font.weight');
    }

    .p-progressbar-determinate .p-progressbar-value {
        height: 100%;
        width: 0%;
        position: absolute;
        display: none;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        transition: width 1s ease-in-out;
    }

    .p-progressbar-determinate .p-progressbar-label {
        display: inline-flex;
    }

    .p-progressbar-indeterminate .p-progressbar-value::before {
        content: '';
        position: absolute;
        background: inherit;
        inset-block-start: 0;
        inset-inline-start: 0;
        inset-block-end: 0;
        will-change: inset-inline-start, inset-inline-end;
        animation: p-progressbar-indeterminate-anim 2.1s cubic-bezier(0.65, 0.815, 0.735, 0.395) infinite;
    }

    .p-progressbar-indeterminate .p-progressbar-value::after {
        content: '';
        position: absolute;
        background: inherit;
        inset-block-start: 0;
        inset-inline-start: 0;
        inset-block-end: 0;
        will-change: inset-inline-start, inset-inline-end;
        animation: p-progressbar-indeterminate-anim-short 2.1s cubic-bezier(0.165, 0.84, 0.44, 1) infinite;
        animation-delay: 1.15s;
    }

    @keyframes p-progressbar-indeterminate-anim {
        0% {
            inset-inline-start: -35%;
            inset-inline-end: 100%;
        }
        60% {
            inset-inline-start: 100%;
            inset-inline-end: -90%;
        }
        100% {
            inset-inline-start: 100%;
            inset-inline-end: -90%;
        }
    }
    @-webkit-keyframes p-progressbar-indeterminate-anim {
        0% {
            inset-inline-start: -35%;
            inset-inline-end: 100%;
        }
        60% {
            inset-inline-start: 100%;
            inset-inline-end: -90%;
        }
        100% {
            inset-inline-start: 100%;
            inset-inline-end: -90%;
        }
    }

    @keyframes p-progressbar-indeterminate-anim-short {
        0% {
            inset-inline-start: -200%;
            inset-inline-end: 100%;
        }
        60% {
            inset-inline-start: 107%;
            inset-inline-end: -8%;
        }
        100% {
            inset-inline-start: 107%;
            inset-inline-end: -8%;
        }
    }
    @-webkit-keyframes p-progressbar-indeterminate-anim-short {
        0% {
            inset-inline-start: -200%;
            inset-inline-end: 100%;
        }
        60% {
            inset-inline-start: 107%;
            inset-inline-end: -8%;
        }
        100% {
            inset-inline-start: 107%;
            inset-inline-end: -8%;
        }
    }
`,cl={root:function(t){var n=t.instance;return["p-progressbar p-component",{"p-progressbar-determinate":n.determinate,"p-progressbar-indeterminate":n.indeterminate}]},value:"p-progressbar-value",label:"p-progressbar-label"},pl=ye.extend({name:"progressbar",style:ul,classes:cl}),fl={name:"BaseProgressBar",extends:H,props:{value:{type:Number,default:null},mode:{type:String,default:"determinate"},showValue:{type:Boolean,default:!0}},style:pl,provide:function(){return{$pcProgressBar:this,$parentInstance:this}}},zt={name:"ProgressBar",extends:fl,inheritAttrs:!1,computed:{progressStyle:function(){return{width:this.value+"%",display:"flex"}},indeterminate:function(){return this.mode==="indeterminate"},determinate:function(){return this.mode==="determinate"},dataP:function(){return Lt({determinate:this.determinate,indeterminate:this.indeterminate})}}},hl=["aria-valuenow","data-p"],ml=["data-p"],bl=["data-p"],gl=["data-p"];function yl(e,t,n,o,r,l){return a(),m("div",f({role:"progressbar",class:e.cx("root"),"aria-valuemin":"0","aria-valuenow":e.value,"aria-valuemax":"100","data-p":l.dataP},e.ptmi("root")),[l.determinate?(a(),m("div",f({key:0,class:e.cx("value"),style:l.progressStyle,"data-p":l.dataP},e.ptm("value")),[e.value!=null&&e.value!==0&&e.showValue?(a(),m("div",f({key:0,class:e.cx("label"),"data-p":l.dataP},e.ptm("label")),[k(e.$slots,"default",{},function(){return[be(j(e.value+"%"),1)]})],16,bl)):w("",!0)],16,ml)):l.indeterminate?(a(),m("div",f({key:1,class:e.cx("value"),"data-p":l.dataP},e.ptm("value")),null,16,gl)):w("",!0)],16,hl)}zt.render=yl;var vl=`
    .p-fileupload input[type='file'] {
        display: none;
    }

    .p-fileupload-advanced {
        border: 1px solid dt('fileupload.border.color');
        border-radius: dt('fileupload.border.radius');
        background: dt('fileupload.background');
        color: dt('fileupload.color');
    }

    .p-fileupload-header {
        display: flex;
        align-items: center;
        padding: dt('fileupload.header.padding');
        background: dt('fileupload.header.background');
        color: dt('fileupload.header.color');
        border-style: solid;
        border-width: dt('fileupload.header.border.width');
        border-color: dt('fileupload.header.border.color');
        border-radius: dt('fileupload.header.border.radius');
        gap: dt('fileupload.header.gap');
    }

    .p-fileupload-content {
        border: 1px solid transparent;
        display: flex;
        flex-direction: column;
        gap: dt('fileupload.content.gap');
        transition: border-color dt('fileupload.transition.duration');
        padding: dt('fileupload.content.padding');
    }

    .p-fileupload-content .p-progressbar {
        width: 100%;
        height: dt('fileupload.progressbar.height');
    }

    .p-fileupload-file-list {
        display: flex;
        flex-direction: column;
        gap: dt('fileupload.filelist.gap');
    }

    .p-fileupload-file {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        padding: dt('fileupload.file.padding');
        border-block-end: 1px solid dt('fileupload.file.border.color');
        gap: dt('fileupload.file.gap');
    }

    .p-fileupload-file:last-child {
        border-block-end: 0;
    }

    .p-fileupload-file-info {
        display: flex;
        flex-direction: column;
        gap: dt('fileupload.file.info.gap');
    }

    .p-fileupload-file-thumbnail {
        flex-shrink: 0;
    }

    .p-fileupload-file-actions {
        margin-inline-start: auto;
    }

    .p-fileupload-highlight {
        border: 1px dashed dt('fileupload.content.highlight.border.color');
    }

    .p-fileupload-basic .p-message {
        margin-block-end: dt('fileupload.basic.gap');
    }

    .p-fileupload-basic-content {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: dt('fileupload.basic.gap');
    }
`,Cl={root:function(t){var n=t.props;return["p-fileupload p-fileupload-".concat(n.mode," p-component")]},header:"p-fileupload-header",pcChooseButton:"p-fileupload-choose-button",pcUploadButton:"p-fileupload-upload-button",pcCancelButton:"p-fileupload-cancel-button",content:"p-fileupload-content",fileList:"p-fileupload-file-list",file:"p-fileupload-file",fileThumbnail:"p-fileupload-file-thumbnail",fileInfo:"p-fileupload-file-info",fileName:"p-fileupload-file-name",fileSize:"p-fileupload-file-size",pcFileBadge:"p-fileupload-file-badge",fileActions:"p-fileupload-file-actions",pcFileRemoveButton:"p-fileupload-file-remove-button",basicContent:"p-fileupload-basic-content"},kl=ye.extend({name:"fileupload",style:vl,classes:Cl}),wl={name:"BaseFileUpload",extends:H,props:{name:{type:String,default:null},url:{type:String,default:null},mode:{type:String,default:"advanced"},multiple:{type:Boolean,default:!1},accept:{type:String,default:null},disabled:{type:Boolean,default:!1},auto:{type:Boolean,default:!1},maxFileSize:{type:Number,default:null},invalidFileSizeMessage:{type:String,default:"{0}: Invalid file size, file size should be smaller than {1}."},invalidFileTypeMessage:{type:String,default:"{0}: Invalid file type, allowed file types: {1}."},fileLimit:{type:Number,default:null},invalidFileLimitMessage:{type:String,default:"Maximum number of files exceeded, limit is {0} at most."},withCredentials:{type:Boolean,default:!1},previewWidth:{type:Number,default:50},chooseLabel:{type:String,default:null},uploadLabel:{type:String,default:null},cancelLabel:{type:String,default:null},customUpload:{type:Boolean,default:!1},showUploadButton:{type:Boolean,default:!0},showCancelButton:{type:Boolean,default:!0},chooseIcon:{type:String,default:void 0},uploadIcon:{type:String,default:void 0},cancelIcon:{type:String,default:void 0},style:null,class:null,chooseButtonProps:{type:null,default:null},uploadButtonProps:{type:Object,default:function(){return{severity:"secondary"}}},cancelButtonProps:{type:Object,default:function(){return{severity:"secondary"}}}},style:kl,provide:function(){return{$pcFileUpload:this,$parentInstance:this}}},Mt={name:"FileContent",hostName:"FileUpload",extends:H,emits:["remove"],props:{files:{type:Array,default:function(){return[]}},badgeSeverity:{type:String,default:"warn"},badgeValue:{type:String,default:null},previewWidth:{type:Number,default:50},templates:{type:null,default:null}},methods:{formatSize:function(t){var n,o=1024,r=3,l=((n=this.$primevue.config.locale)===null||n===void 0?void 0:n.fileSizeTypes)||["B","KB","MB","GB","TB","PB","EB","ZB","YB"];if(t===0)return"0 ".concat(l[0]);var s=Math.floor(Math.log(t)/Math.log(o)),d=parseFloat((t/Math.pow(o,s)).toFixed(r));return"".concat(d," ").concat(l[s])}},components:{Button:_,Badge:at,TimesIcon:dt}},Sl=["alt","src","width"];function Pl(e,t,n,o,r,l){var s=E("Badge"),d=E("TimesIcon"),u=E("Button");return a(!0),m(O,null,U(n.files,function(c,i){return a(),m("div",f({key:c.name+c.type+c.size,class:e.cx("file")},{ref_for:!0},e.ptm("file")),[v("img",f({role:"presentation",class:e.cx("fileThumbnail"),alt:c.name,src:c.objectURL,width:n.previewWidth},{ref_for:!0},e.ptm("fileThumbnail")),null,16,Sl),v("div",f({class:e.cx("fileInfo")},{ref_for:!0},e.ptm("fileInfo")),[v("div",f({class:e.cx("fileName")},{ref_for:!0},e.ptm("fileName")),j(c.name),17),v("span",f({class:e.cx("fileSize")},{ref_for:!0},e.ptm("fileSize")),j(l.formatSize(c.size)),17)],16),S(s,{value:n.badgeValue,class:M(e.cx("pcFileBadge")),severity:n.badgeSeverity,unstyled:e.unstyled,pt:e.ptm("pcFileBadge")},null,8,["value","class","severity","unstyled","pt"]),v("div",f({class:e.cx("fileActions")},{ref_for:!0},e.ptm("fileActions")),[S(u,{onClick:function(C){return e.$emit("remove",i)},text:"",rounded:"",severity:"danger",class:M(e.cx("pcFileRemoveButton")),unstyled:e.unstyled,pt:e.ptm("pcFileRemoveButton")},{icon:y(function(h){return[n.templates.fileremoveicon?(a(),g(x(n.templates.fileremoveicon),{key:0,class:M(h.class),file:c,index:i},null,8,["class","file","index"])):(a(),g(d,f({key:1,class:h.class,"aria-hidden":"true"},{ref_for:!0},e.ptm("pcFileRemoveButton").icon),null,16,["class"]))]}),_:2},1032,["onClick","class","unstyled","pt"])],16)],16)}),128)}Mt.render=Pl;function ze(e){return Tl(e)||Ml(e)||Tt(e)||zl()}function zl(){throw new TypeError(`Invalid attempt to spread non-iterable instance.
In order to be iterable, non-array objects must have a [Symbol.iterator]() method.`)}function Ml(e){if(typeof Symbol<"u"&&e[Symbol.iterator]!=null||e["@@iterator"]!=null)return Array.from(e)}function Tl(e){if(Array.isArray(e))return Fe(e)}function fe(e,t){var n=typeof Symbol<"u"&&e[Symbol.iterator]||e["@@iterator"];if(!n){if(Array.isArray(e)||(n=Tt(e))||t){n&&(e=n);var o=0,r=function(){};return{s:r,n:function(){return o>=e.length?{done:!0}:{done:!1,value:e[o++]}},e:function(c){throw c},f:r}}throw new TypeError(`Invalid attempt to iterate non-iterable instance.
In order to be iterable, non-array objects must have a [Symbol.iterator]() method.`)}var l,s=!0,d=!1;return{s:function(){n=n.call(e)},n:function(){var c=n.next();return s=c.done,c},e:function(c){d=!0,l=c},f:function(){try{s||n.return==null||n.return()}finally{if(d)throw l}}}}function Tt(e,t){if(e){if(typeof e=="string")return Fe(e,t);var n={}.toString.call(e).slice(8,-1);return n==="Object"&&e.constructor&&(n=e.constructor.name),n==="Map"||n==="Set"?Array.from(e):n==="Arguments"||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)?Fe(e,t):void 0}}function Fe(e,t){(t==null||t>e.length)&&(t=e.length);for(var n=0,o=Array(t);n<t;n++)o[n]=e[n];return o}var Ft={name:"FileUpload",extends:wl,inheritAttrs:!1,emits:["select","uploader","before-upload","progress","upload","error","before-send","clear","remove","remove-uploaded-file"],duplicateIEEvent:!1,data:function(){return{uploadedFileCount:0,files:[],messages:[],focused:!1,progress:null,uploadedFiles:[]}},methods:{upload:function(){this.hasFiles&&this.uploader()},onBasicUploaderClick:function(t){t.button===0&&this.$refs.fileInput.click()},onFileSelect:function(t){if(t.type!=="drop"&&this.isIE11()&&this.duplicateIEEvent){this.duplicateIEEvent=!1;return}this.isBasic&&this.hasFiles&&(this.files=[]),this.messages=[],this.files=this.files||[];var n=t.dataTransfer?t.dataTransfer.files:t.target.files,o=fe(n),r;try{for(o.s();!(r=o.n()).done;){var l=r.value;!this.isFileSelected(l)&&!this.isFileLimitExceeded()&&this.validate(l)&&(this.isImage(l)&&(l.objectURL=window.URL.createObjectURL(l)),this.files.push(l))}}catch(s){o.e(s)}finally{o.f()}this.$emit("select",{originalEvent:t,files:this.files}),this.fileLimit&&this.checkFileLimit(),this.auto&&this.hasFiles&&!this.isFileLimitExceeded()&&this.uploader(),t.type!=="drop"&&this.isIE11()?this.clearIEInput():this.clearInputElement()},choose:function(){this.$refs.fileInput.click()},uploader:function(){var t=this;if(this.customUpload)this.fileLimit&&(this.uploadedFileCount+=this.files.length),this.$emit("uploader",{files:this.files});else{var n=new XMLHttpRequest,o=new FormData;this.$emit("before-upload",{xhr:n,formData:o});var r=fe(this.files),l;try{for(r.s();!(l=r.n()).done;){var s=l.value;o.append(this.name,s,s.name)}}catch(d){r.e(d)}finally{r.f()}n.upload.addEventListener("progress",function(d){d.lengthComputable&&(t.progress=Math.round(d.loaded*100/d.total)),t.$emit("progress",{originalEvent:d,progress:t.progress})}),n.onreadystatechange=function(){if(n.readyState===4){if(t.progress=0,n.status>=200&&n.status<300){var d;t.fileLimit&&(t.uploadedFileCount+=t.files.length),t.$emit("upload",{xhr:n,files:t.files}),(d=t.uploadedFiles).push.apply(d,ze(t.files))}else t.$emit("error",{xhr:n,files:t.files});t.clear()}},this.url&&(n.open("POST",this.url,!0),this.$emit("before-send",{xhr:n,formData:o}),n.withCredentials=this.withCredentials,n.send(o))}},clear:function(){this.files=[],this.messages=null,this.$emit("clear"),this.isAdvanced&&this.clearInputElement()},onFocus:function(){this.focused=!0},onBlur:function(){this.focused=!1},isFileSelected:function(t){if(this.files&&this.files.length){var n=fe(this.files),o;try{for(n.s();!(o=n.n()).done;){var r=o.value;if(r.name+r.type+r.size===t.name+t.type+t.size)return!0}}catch(l){n.e(l)}finally{n.f()}}return!1},isIE11:function(){return!!window.MSInputMethodContext&&!!document.documentMode},validate:function(t){return this.accept&&!this.isFileTypeValid(t)?(this.messages.push(this.invalidFileTypeMessage.replace("{0}",t.name).replace("{1}",this.accept)),!1):this.maxFileSize&&t.size>this.maxFileSize?(this.messages.push(this.invalidFileSizeMessage.replace("{0}",t.name).replace("{1}",this.formatSize(this.maxFileSize))),!1):!0},isFileTypeValid:function(t){var n=this.accept.split(",").map(function(d){return d.trim()}),o=fe(n),r;try{for(o.s();!(r=o.n()).done;){var l=r.value,s=this.isWildcard(l)?this.getTypeClass(t.type)===this.getTypeClass(l):t.type==l||this.getFileExtension(t).toLowerCase()===l.toLowerCase();if(s)return!0}}catch(d){o.e(d)}finally{o.f()}return!1},getTypeClass:function(t){return t.substring(0,t.indexOf("/"))},isWildcard:function(t){return t.indexOf("*")!==-1},getFileExtension:function(t){return"."+t.name.split(".").pop()},isImage:function(t){return/^image\//.test(t.type)},onDragEnter:function(t){this.disabled||(t.stopPropagation(),t.preventDefault())},onDragOver:function(t){this.disabled||(!this.isUnstyled&&Xt(this.$refs.content,"p-fileupload-highlight"),this.$refs.content.setAttribute("data-p-highlight",!0),t.stopPropagation(),t.preventDefault())},onDragLeave:function(){this.disabled||(!this.isUnstyled&&Ne(this.$refs.content,"p-fileupload-highlight"),this.$refs.content.setAttribute("data-p-highlight",!1))},onDrop:function(t){if(!this.disabled){!this.isUnstyled&&Ne(this.$refs.content,"p-fileupload-highlight"),this.$refs.content.setAttribute("data-p-highlight",!1),t.stopPropagation(),t.preventDefault();var n=t.dataTransfer?t.dataTransfer.files:t.target.files,o=this.multiple||n&&n.length===1;o&&this.onFileSelect(t)}},remove:function(t){this.clearInputElement();var n=this.files.splice(t,1)[0];this.files=ze(this.files),this.$emit("remove",{file:n,files:this.files})},removeUploadedFile:function(t){var n=this.uploadedFiles.splice(t,1)[0];this.uploadedFiles=ze(this.uploadedFiles),this.$emit("remove-uploaded-file",{file:n,files:this.uploadedFiles})},clearInputElement:function(){this.$refs.fileInput.value=""},clearIEInput:function(){this.$refs.fileInput&&(this.duplicateIEEvent=!0,this.$refs.fileInput.value="")},formatSize:function(t){var n,o=1024,r=3,l=((n=this.$primevue.config.locale)===null||n===void 0?void 0:n.fileSizeTypes)||["B","KB","MB","GB","TB","PB","EB","ZB","YB"];if(t===0)return"0 ".concat(l[0]);var s=Math.floor(Math.log(t)/Math.log(o)),d=parseFloat((t/Math.pow(o,s)).toFixed(r));return"".concat(d," ").concat(l[s])},isFileLimitExceeded:function(){return this.fileLimit&&this.fileLimit<=this.files.length+this.uploadedFileCount&&this.focused&&(this.focused=!1),this.fileLimit&&this.fileLimit<this.files.length+this.uploadedFileCount},checkFileLimit:function(){this.isFileLimitExceeded()&&this.messages.push(this.invalidFileLimitMessage.replace("{0}",this.fileLimit.toString()))},onMessageClose:function(){this.messages=null}},computed:{isAdvanced:function(){return this.mode==="advanced"},isBasic:function(){return this.mode==="basic"},chooseButtonClass:function(){return[this.cx("pcChooseButton"),this.class]},basicFileChosenLabel:function(){var t;if(this.auto)return this.chooseButtonLabel;if(this.hasFiles){var n;return this.files&&this.files.length===1?this.files[0].name:(n=this.$primevue.config.locale)===null||n===void 0||(n=n.fileChosenMessage)===null||n===void 0?void 0:n.replace("{0}",this.files.length)}return((t=this.$primevue.config.locale)===null||t===void 0?void 0:t.noFileChosenMessage)||""},hasFiles:function(){return this.files&&this.files.length>0},hasUploadedFiles:function(){return this.uploadedFiles&&this.uploadedFiles.length>0},chooseDisabled:function(){return this.disabled||this.fileLimit&&this.fileLimit<=this.files.length+this.uploadedFileCount},uploadDisabled:function(){return this.disabled||!this.hasFiles||this.fileLimit&&this.fileLimit<this.files.length},cancelDisabled:function(){return this.disabled||!this.hasFiles},chooseButtonLabel:function(){return this.chooseLabel||this.$primevue.config.locale.choose},uploadButtonLabel:function(){return this.uploadLabel||this.$primevue.config.locale.upload},cancelButtonLabel:function(){return this.cancelLabel||this.$primevue.config.locale.cancel},completedLabel:function(){return this.$primevue.config.locale.completed},pendingLabel:function(){return this.$primevue.config.locale.pending}},components:{Button:_,ProgressBar:zt,Message:on,FileContent:Mt,PlusIcon:Nt,UploadIcon:Pt,TimesIcon:dt},directives:{ripple:st}},Fl=["multiple","accept","disabled"],Ol=["accept","disabled","multiple"];function xl(e,t,n,o,r,l){var s=E("Button"),d=E("ProgressBar"),u=E("Message"),c=E("FileContent");return l.isAdvanced?(a(),m("div",f({key:0,class:e.cx("root")},e.ptmi("root")),[v("input",f({ref:"fileInput",type:"file",onChange:t[0]||(t[0]=function(){return l.onFileSelect&&l.onFileSelect.apply(l,arguments)}),multiple:e.multiple,accept:e.accept,disabled:l.chooseDisabled},e.ptm("input")),null,16,Fl),v("div",f({class:e.cx("header")},e.ptm("header")),[k(e.$slots,"header",{files:r.files,uploadedFiles:r.uploadedFiles,chooseCallback:l.choose,uploadCallback:l.uploader,clearCallback:l.clear},function(){return[S(s,f({label:l.chooseButtonLabel,class:l.chooseButtonClass,style:e.style,disabled:e.disabled,unstyled:e.unstyled,onClick:l.choose,onKeydown:We(l.choose,["enter"]),onFocus:l.onFocus,onBlur:l.onBlur},e.chooseButtonProps,{pt:e.ptm("pcChooseButton")}),{icon:y(function(i){return[k(e.$slots,"chooseicon",{},function(){return[(a(),g(x(e.chooseIcon?"span":"PlusIcon"),f({class:[i.class,e.chooseIcon],"aria-hidden":"true"},e.ptm("pcChooseButton").icon),null,16,["class"]))]})]}),_:3},16,["label","class","style","disabled","unstyled","onClick","onKeydown","onFocus","onBlur","pt"]),e.showUploadButton?(a(),g(s,f({key:0,class:e.cx("pcUploadButton"),label:l.uploadButtonLabel,onClick:l.uploader,disabled:l.uploadDisabled,unstyled:e.unstyled},e.uploadButtonProps,{pt:e.ptm("pcUploadButton")}),{icon:y(function(i){return[k(e.$slots,"uploadicon",{},function(){return[(a(),g(x(e.uploadIcon?"span":"UploadIcon"),f({class:[i.class,e.uploadIcon],"aria-hidden":"true"},e.ptm("pcUploadButton").icon,{"data-pc-section":"uploadbuttonicon"}),null,16,["class"]))]})]}),_:3},16,["class","label","onClick","disabled","unstyled","pt"])):w("",!0),e.showCancelButton?(a(),g(s,f({key:1,class:e.cx("pcCancelButton"),label:l.cancelButtonLabel,onClick:l.clear,disabled:l.cancelDisabled,unstyled:e.unstyled},e.cancelButtonProps,{pt:e.ptm("pcCancelButton")}),{icon:y(function(i){return[k(e.$slots,"cancelicon",{},function(){return[(a(),g(x(e.cancelIcon?"span":"TimesIcon"),f({class:[i.class,e.cancelIcon],"aria-hidden":"true"},e.ptm("pcCancelButton").icon,{"data-pc-section":"cancelbuttonicon"}),null,16,["class"]))]})]}),_:3},16,["class","label","onClick","disabled","unstyled","pt"])):w("",!0)]})],16),v("div",f({ref:"content",class:e.cx("content"),onDragenter:t[1]||(t[1]=function(){return l.onDragEnter&&l.onDragEnter.apply(l,arguments)}),onDragover:t[2]||(t[2]=function(){return l.onDragOver&&l.onDragOver.apply(l,arguments)}),onDragleave:t[3]||(t[3]=function(){return l.onDragLeave&&l.onDragLeave.apply(l,arguments)}),onDrop:t[4]||(t[4]=function(){return l.onDrop&&l.onDrop.apply(l,arguments)})},e.ptm("content"),{"data-p-highlight":!1}),[k(e.$slots,"content",{files:r.files,uploadedFiles:r.uploadedFiles,removeUploadedFileCallback:l.removeUploadedFile,removeFileCallback:l.remove,progress:r.progress,messages:r.messages},function(){return[l.hasFiles?(a(),g(d,{key:0,value:r.progress,showValue:!1,unstyled:e.unstyled,pt:e.ptm("pcProgressbar")},null,8,["value","unstyled","pt"])):w("",!0),(a(!0),m(O,null,U(r.messages,function(i){return a(),g(u,{key:i,severity:"error",onClose:l.onMessageClose,unstyled:e.unstyled,pt:e.ptm("pcMessage")},{default:y(function(){return[be(j(i),1)]}),_:2},1032,["onClose","unstyled","pt"])}),128)),l.hasFiles?(a(),m("div",{key:1,class:M(e.cx("fileList"))},[S(c,{files:r.files,onRemove:l.remove,badgeValue:l.pendingLabel,previewWidth:e.previewWidth,templates:e.$slots,unstyled:e.unstyled,pt:e.pt},null,8,["files","onRemove","badgeValue","previewWidth","templates","unstyled","pt"])],2)):w("",!0),l.hasUploadedFiles?(a(),m("div",{key:2,class:M(e.cx("fileList"))},[S(c,{files:r.uploadedFiles,onRemove:l.removeUploadedFile,badgeValue:l.completedLabel,badgeSeverity:"success",previewWidth:e.previewWidth,templates:e.$slots,unstyled:e.unstyled,pt:e.pt},null,8,["files","onRemove","badgeValue","previewWidth","templates","unstyled","pt"])],2)):w("",!0)]}),e.$slots.empty&&!l.hasFiles&&!l.hasUploadedFiles?(a(),m("div",Ke(f({key:0},e.ptm("empty"))),[k(e.$slots,"empty")],16)):w("",!0)],16)],16)):l.isBasic?(a(),m("div",f({key:1,class:e.cx("root")},e.ptmi("root")),[(a(!0),m(O,null,U(r.messages,function(i){return a(),g(u,{key:i,severity:"error",onClose:l.onMessageClose,unstyled:e.unstyled,pt:e.ptm("pcMessage")},{default:y(function(){return[be(j(i),1)]}),_:2},1032,["onClose","unstyled","pt"])}),128)),v("div",f({class:e.cx("basicContent")},e.ptm("basicContent")),[S(s,f({label:l.chooseButtonLabel,class:l.chooseButtonClass,style:e.style,disabled:e.disabled,unstyled:e.unstyled,onMouseup:l.onBasicUploaderClick,onKeydown:We(l.choose,["enter"]),onFocus:l.onFocus,onBlur:l.onBlur},e.chooseButtonProps,{pt:e.ptm("pcChooseButton")}),{icon:y(function(i){return[k(e.$slots,"chooseicon",{},function(){return[(a(),g(x(e.chooseIcon?"span":"PlusIcon"),f({class:[i.class,e.chooseIcon],"aria-hidden":"true"},e.ptm("pcChooseButton").icon),null,16,["class"]))]})]}),_:3},16,["label","class","style","disabled","unstyled","onMouseup","onKeydown","onFocus","onBlur","pt"]),e.auto?w("",!0):k(e.$slots,"filelabel",{key:0,class:M(e.cx("filelabel")),files:r.files},function(){return[v("span",{class:M(e.cx("filelabel"))},j(l.basicFileChosenLabel),3)]}),v("input",f({ref:"fileInput",type:"file",accept:e.accept,disabled:e.disabled,multiple:e.multiple,onChange:t[5]||(t[5]=function(){return l.onFileSelect&&l.onFileSelect.apply(l,arguments)}),onFocus:t[6]||(t[6]=function(){return l.onFocus&&l.onFocus.apply(l,arguments)}),onBlur:t[7]||(t[7]=function(){return l.onBlur&&l.onBlur.apply(l,arguments)})},e.ptm("input")),null,16,Ol)],16)],16)):w("",!0)}Ft.render=xl;const Il=["src"],Kl={class:"flex"},Bl={class:"flex flex-col gap-4"},rt=Y({__name:"BaseFileList",props:Q({baseUrl:{},client:{type:[Function,Object]},type:{default:"file"},invalid:{type:Boolean,default:!1},initialItems:{default:()=>[]},title:{}},{modelValue:{default:()=>[]},modelModifiers:{}}),emits:["update:modelValue"],setup(e){const t=()=>({key:"",order:0,type:e.type}),n=R([]),o=ee(e,"modelValue"),r=ve(()=>e.type==="image"?"image/*":void 0),l=R(!1),s=R([]),d=({files:i})=>{s.value=i},u=async()=>{const i=new FormData;i.append("file",s.value[0]);try{const h=await e.client.post(e.baseUrl,i);h.data&&(o.value=[...o.value,{...h.data,pivot:t()}]),l.value=!1}catch(h){console.error("File upload failed:",h)}},c=async()=>{o.value=o.value.filter(i=>!n.value.includes(i)),n.value=[]};return(i,h)=>{const C=q,F=Oe,$=ge,N=_,D=ut,V=Ft,J=rn;return a(),m(O,null,[v("div",null,[v("h3",null,j(i.title),1),S(D,{value:o.value,showGridlines:"",stripedRows:"",selectionMode:"multiple",selection:n.value,"onUpdate:selection":h[1]||(h[1]=I=>n.value=I),size:"small"},{footer:y(()=>[v("div",Kl,[h[3]||(h[3]=v("div",{class:"flex-1"},null,-1)),S(N,{type:"button",icon:"pi pi-trash",text:"",size:"small",disabled:n.value.length===0,onClick:c},null,8,["disabled"]),S(N,{type:"button",icon:"pi pi-plus",text:"",size:"small",onClick:h[0]||(h[0]=I=>l.value=!0)})])]),default:y(()=>[S(C,{selectionMode:"multiple"}),S(C,{field:"name",header:"Name"}),i.type==="image"?(a(),g(C,{key:0,field:"preview",header:"Preview"},{body:y(({data:I})=>[v("img",{src:I.url,alt:"Preview",class:"object-contain"},null,8,Il)]),_:1})):w("",!0),S(C,{field:"pivot.order",header:"Order"},{body:y(({data:I})=>[S(F,{modelValue:I.pivot.order,"onUpdate:modelValue":A=>I.pivot.order=A},null,8,["modelValue","onUpdate:modelValue"])]),_:1}),S(C,{field:"pivot.key",header:"Key"},{body:y(({data:I})=>[S($,{modelValue:I.pivot.key,"onUpdate:modelValue":A=>I.pivot.key=A},null,8,["modelValue","onUpdate:modelValue"])]),_:1})]),_:1},8,["value","selection"])]),S(J,{visible:l.value,"onUpdate:visible":h[2]||(h[2]=I=>l.value=I),modal:"",header:"Создать файл"},{footer:y(()=>[S(N,{label:"Сохранить",onClick:u})]),default:y(()=>[v("div",Bl,[S(V,{customUpload:"",mode:"basic",onSelect:d,accept:r.value},null,8,["accept"])])]),_:1},8,["visible"])],64)}}}),El=e=>({fields:ve(()=>[{component:Pe,key:"name",props:{type:"name",fluid:!0,autocomplete:"dish_name",label:"Название",name:"name"},rule:mt({required_error:"Название обязательно"}).min(1,"Название обязательно")},{component:Pe,key:"slug",props:{type:"slug",label:"Slug",fluid:!0,autocomplete:"dish_slug",name:"slug"}},{component:ot,key:"order",props:{type:"order",label:"Порядок",fluid:!0,autocomplete:"dish_order",name:"order"}},{component:ot,key:"price",props:{type:"number",label:"Цена",fluid:!0,autocomplete:"dish_price",name:"price"}},{component:Pe,key:"description",props:{type:"description",label:"Описание",fluid:!0,autocomplete:"dish_description",name:"description"}},{component:rt,props:{type:"file",baseUrl:Ge,client:me,title:"Файлы"},key:"files"},{component:rt,props:{type:"image",baseUrl:Ge,client:me,title:"Изображения"},key:"images"}])}),jl=Y({__name:"DishesDetail",props:{id:{}},setup(e){const{fields:t}=El(),n=R(),o=["files","images"];return(r,l)=>(a(),g(St,{baseUrl:K(tl),client:K(me),entity:"dishes",fields:K(t),form:n.value,"onUpdate:form":l[0]||(l[0]=s=>n.value=s),relations:o,id:r.id},null,8,["baseUrl","client","fields","form","id"]))}}),Ll=e=>({fields:ve(()=>[{component:ge,key:"name",props:{type:"name",placeholder:"Название",fluid:!0,autocomplete:"category_name",name:"name"},rule:mt({required_error:"Название обязательно"}).min(1,"Название обязательно")},{component:Xn,props:{title:"Категории",columns:[{field:"name",header:"Название"}],detailComponent:{component:Qn,props:{onSave:n=>{console.log(n)}}},initialValues:{parent_id:e.id}},key:"children"},{component:el,props:{title:"Блюда",entity:"dishes",columns:[{field:"name",header:"Название"},{field:"order",header:"Порядок"}],detailComponent:{component:jl,props:{}},initialValues:{category_id:e.id}},key:"dishes"}])});export{Qn as _,Pe as a,ot as b,rt as c,St as d,el as e,Xn as f,Be as s};
