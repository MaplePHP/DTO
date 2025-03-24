"use strict";(self.webpackChunkdto=self.webpackChunkdto||[]).push([[3976],{7879:(e,n,r)=>{r.r(n),r.d(n,{assets:()=>o,contentTitle:()=>a,default:()=>h,frontMatter:()=>l,metadata:()=>s,toc:()=>d});const s=JSON.parse('{"id":"intro","title":"Darn Tidy Object (DTO)","description":"DTO stands for Darn Tidy Object, a playful twist on the traditional Data Transfer Object. But this isn\u2019t your average DTO. It\u2019s a fully-loaded toolkit for traversing, transforming, and tidying up structured data in PHP with style, power, and simplicity.","source":"@site/docs/intro.md","sourceDirName":".","slug":"/intro","permalink":"/DTO/docs/intro","draft":false,"unlisted":false,"tags":[],"version":"current","sidebarPosition":1,"frontMatter":{"sidebar_position":1},"sidebar":"tutorialSidebar","next":{"title":"Traverse Collection","permalink":"/DTO/docs/traverse"}}');var t=r(4848),i=r(8453);const l={sidebar_position:1},a="Darn Tidy Object (DTO)",o={},d=[{value:"\ud83d\udce6 Installation",id:"-installation",level:2},{value:"Why DTO?",id:"why-dto",level:2},{value:"How It Works",id:"how-it-works",level:2},{value:"Before DTO",id:"before-dto",level:3},{value:"With DTO",id:"with-dto",level:3},{value:"\u2728 Core Features",id:"-core-features",level:2},{value:"Smart Data Traversal",id:"smart-data-traversal",level:3},{value:"Built-In Data Transformation",id:"built-in-data-transformation",level:3},{value:"Strings (<code>str</code>)",id:"strings-str",level:4},{value:"Numbers (<code>num</code>)",id:"numbers-num",level:4},{value:"Dates (<code>clock</code>)",id:"dates-clock",level:4},{value:"HTML DOM Builder (<code>dom</code>)",id:"html-dom-builder-dom",level:4},{value:"Built-In Collection Support",id:"built-in-collection-support",level:3},{value:"Modify Data on the Fly",id:"modify-data-on-the-fly",level:3}];function c(e){const n={a:"a",code:"code",h1:"h1",h2:"h2",h3:"h3",h4:"h4",header:"header",hr:"hr",li:"li",p:"p",pre:"pre",strong:"strong",ul:"ul",...(0,i.R)(),...e.components};return(0,t.jsxs)(t.Fragment,{children:[(0,t.jsx)(n.header,{children:(0,t.jsx)(n.h1,{id:"darn-tidy-object-dto",children:"Darn Tidy Object (DTO)"})}),"\n",(0,t.jsxs)(n.p,{children:["DTO stands for ",(0,t.jsx)(n.strong,{children:"Darn Tidy Object"}),", a playful twist on the traditional Data Transfer Object. But this isn\u2019t your average DTO. It\u2019s a fully-loaded toolkit for ",(0,t.jsx)(n.strong,{children:"traversing, transforming, and tidying up structured data"})," in PHP with style, power, and simplicity."]}),"\n",(0,t.jsx)(n.h2,{id:"-installation",children:"\ud83d\udce6 Installation"}),"\n",(0,t.jsx)(n.pre,{children:(0,t.jsx)(n.code,{className:"language-bash",children:"composer require maplephp/dto\n"})}),"\n",(0,t.jsx)(n.h2,{id:"why-dto",children:"Why DTO?"}),"\n",(0,t.jsx)(n.p,{children:"Working with structured data in PHP often means:"}),"\n",(0,t.jsxs)(n.ul,{children:["\n",(0,t.jsx)(n.li,{children:"Deep arrays"}),"\n",(0,t.jsx)(n.li,{children:"Missing keys"}),"\n",(0,t.jsx)(n.li,{children:"Manual transformations"}),"\n",(0,t.jsx)(n.li,{children:"Repetitive helper functions"}),"\n",(0,t.jsxs)(n.li,{children:["And a sprinkle of ",(0,t.jsx)(n.code,{children:"isset()"})," nightmares..."]}),"\n"]}),"\n",(0,t.jsx)(n.p,{children:"DTO eliminates all of that with:"}),"\n",(0,t.jsxs)(n.ul,{children:["\n",(0,t.jsxs)(n.li,{children:["\u2705 ",(0,t.jsx)(n.strong,{children:"Fluent, chainable data access"})," \u2013 Traverse deeply nested arrays like a pro."]}),"\n",(0,t.jsxs)(n.li,{children:["\u2705 ",(0,t.jsx)(n.strong,{children:"Built-in transformation helpers"})," \u2013 Strings, numbers, dates, and even DOM elements."]}),"\n",(0,t.jsxs)(n.li,{children:["\u2705 ",(0,t.jsx)(n.strong,{children:"Automatic fallbacks"})," \u2013 No more boilerplate ",(0,t.jsx)(n.code,{children:"isset()"})," or ",(0,t.jsx)(n.code,{children:"array_key_exists()"})," logic."]}),"\n",(0,t.jsxs)(n.li,{children:["\u2705 ",(0,t.jsx)(n.strong,{children:"Optional immutability"})," \u2013 Keep your original data safe when needed."]}),"\n",(0,t.jsxs)(n.li,{children:["\u2705 ",(0,t.jsx)(n.strong,{children:"Multibyte-safe polyfills"})," \u2013 Works reliably across all PHP environments."]}),"\n",(0,t.jsxs)(n.li,{children:["\u2705 ",(0,t.jsx)(n.strong,{children:"More than a DTO"})," \u2013 Think: lightweight data object meets utility powerhouse."]}),"\n"]}),"\n",(0,t.jsx)(n.hr,{}),"\n",(0,t.jsx)(n.h2,{id:"how-it-works",children:"How It Works"}),"\n",(0,t.jsx)(n.p,{children:"DTO wraps your data arrays into a powerful, fluent object structure. Instead of cluttered array access, your code becomes expressive and self-documenting."}),"\n",(0,t.jsx)(n.h3,{id:"before-dto",children:"Before DTO"}),"\n",(0,t.jsx)(n.pre,{children:(0,t.jsx)(n.code,{className:"language-php",children:"$name = isset($data['user']['profile']['name'])\n    ? ucfirst(strip_tags($data['user']['profile']['name']))\n    : 'Guest';\n"})}),"\n",(0,t.jsx)(n.h3,{id:"with-dto",children:"With DTO"}),"\n",(0,t.jsx)(n.pre,{children:(0,t.jsx)(n.code,{className:"language-php",children:"$name = $obj->user->profile->name\n    ->strStripTags()\n    ->strUcFirst()\n    ->fallback('Guest')\n    ->get();\n"})}),"\n",(0,t.jsx)(n.p,{children:"Much tidier, right?"}),"\n",(0,t.jsx)(n.hr,{}),"\n",(0,t.jsx)(n.h2,{id:"-core-features",children:"\u2728 Core Features"}),"\n",(0,t.jsx)(n.h3,{id:"smart-data-traversal",children:"Smart Data Traversal"}),"\n",(0,t.jsx)(n.p,{children:"Access deeply nested data without ever worrying about undefined keys."}),"\n",(0,t.jsx)(n.pre,{children:(0,t.jsx)(n.code,{className:"language-php",children:"echo $obj->article->tagline->strToUpper();  \n// Result: 'HELLO WORLD'\n\necho $obj->article->content->strExcerpt()->strUcFirst();  \n// Result: 'Lorem ipsum dolor sit amet...'\n"})}),"\n",(0,t.jsx)(n.p,{children:(0,t.jsx)(n.a,{href:"/docs/traverse",children:"Explore collections"})}),"\n",(0,t.jsx)(n.hr,{}),"\n",(0,t.jsx)(n.h3,{id:"built-in-data-transformation",children:"Built-In Data Transformation"}),"\n",(0,t.jsx)(n.p,{children:"Transform values directly using built-in helpers like:"}),"\n",(0,t.jsxs)(n.h4,{id:"strings-str",children:["Strings (",(0,t.jsx)(n.code,{children:"str"}),")"]}),"\n",(0,t.jsx)(n.pre,{children:(0,t.jsx)(n.code,{className:"language-php",children:"echo $obj->title->strSlug();  \n// Result: 'my-awesome-title'\n"})}),"\n",(0,t.jsx)(n.p,{children:(0,t.jsx)(n.a,{href:"/docs/format-string",children:"Explore string formats"})}),"\n",(0,t.jsxs)(n.h4,{id:"numbers-num",children:["Numbers (",(0,t.jsx)(n.code,{children:"num"}),")"]}),"\n",(0,t.jsx)(n.pre,{children:(0,t.jsx)(n.code,{className:"language-php",children:"echo $obj->filesize->numToFilesize();  \n// Result: '1.95 kb'\n\necho $obj->price->numRound(2)->numToCurrency(\"USD\");  \n// Result: $1,999.99\n"})}),"\n",(0,t.jsx)(n.p,{children:(0,t.jsx)(n.a,{href:"/docs/format-number",children:"Explore number formats"})}),"\n",(0,t.jsxs)(n.h4,{id:"dates-clock",children:["Dates (",(0,t.jsx)(n.code,{children:"clock"}),")"]}),"\n",(0,t.jsx)(n.pre,{children:(0,t.jsx)(n.code,{className:"language-php",children:"echo $obj->created_at->clockFormat('d M, Y', 'sv_SE');  \n// Result: '21 augusti 2025'\n\necho $obj->created_at->clockIsToday();  \n// Result: true\n"})}),"\n",(0,t.jsx)(n.p,{children:(0,t.jsx)(n.a,{href:"/docs/format-clock",children:"Explore clock formats"})}),"\n",(0,t.jsxs)(n.h4,{id:"html-dom-builder-dom",children:["HTML DOM Builder (",(0,t.jsx)(n.code,{children:"dom"}),")"]}),"\n",(0,t.jsx)(n.pre,{children:(0,t.jsx)(n.code,{className:"language-php",children:'echo $obj->heading->domTag("h1.title");  \n// Result: <h1 class="title">My Heading</h1>\n'})}),"\n",(0,t.jsx)(n.p,{children:"Or nest elements with ease:"}),"\n",(0,t.jsx)(n.pre,{children:(0,t.jsx)(n.code,{className:"language-php",children:'echo $obj->title->domTag("h1.title")->domTag("header");  \n// Result: <header><h1 class="title">Hello</h1></header>\n'})}),"\n",(0,t.jsx)(n.p,{children:(0,t.jsx)(n.a,{href:"/docs/format-dom",children:"Explore DOM formats"})}),"\n",(0,t.jsx)(n.hr,{}),"\n",(0,t.jsx)(n.h3,{id:"built-in-collection-support",children:"Built-In Collection Support"}),"\n",(0,t.jsx)(n.p,{children:"Work with arrays of objects just as cleanly:"}),"\n",(0,t.jsx)(n.pre,{children:(0,t.jsx)(n.code,{className:"language-php",children:"foreach ($obj->users->fetch() as $user) {\n    echo $user->firstName->strUcFirst();\n}\n"})}),"\n",(0,t.jsx)(n.hr,{}),"\n",(0,t.jsx)(n.h3,{id:"modify-data-on-the-fly",children:"Modify Data on the Fly"}),"\n",(0,t.jsx)(n.p,{children:"Change values directly without verbose conditionals:"}),"\n",(0,t.jsx)(n.pre,{children:(0,t.jsx)(n.code,{className:"language-php",children:"$updated = $obj->shoppingList->replace([0 => 'Shampoo']);\nprint_r($updated->toArray());\n"})}),"\n",(0,t.jsx)(n.hr,{}),"\n",(0,t.jsx)(n.p,{children:"Now go forth, write cleaner code, and let DTO handle the messy parts."})]})}function h(e={}){const{wrapper:n}={...(0,i.R)(),...e.components};return n?(0,t.jsx)(n,{...e,children:(0,t.jsx)(c,{...e})}):c(e)}},8453:(e,n,r)=>{r.d(n,{R:()=>l,x:()=>a});var s=r(6540);const t={},i=s.createContext(t);function l(e){const n=s.useContext(i);return s.useMemo((function(){return"function"==typeof e?e(n):{...n,...e}}),[n,e])}function a(e){let n;return n=e.disableParentContext?"function"==typeof e.components?e.components(t):e.components||t:l(e.components),s.createElement(i.Provider,{value:n},e.children)}}}]);