import React, {lazy, Suspense} from "react";
import LoadingScreen from "__JS_ROOT__/src/components/LoadingScreen";

const Loadable = (Component) => (props) => (
    <Suspense fallback={<LoadingScreen/>}>
        <Component {...props} />
    </Suspense>
);
const __MODEL__List = Loadable(lazy(() => import("__JS_ROOT__/src/pages/dashboard/__LC_MODEL__/__MODEL__ListPage")));
const __MODEL__Add = Loadable(lazy(() => import("__JS_ROOT__/src/pages/dashboard/__LC_MODEL__/__MODEL__AddPage")));
const __MODEL__Edit = Loadable(lazy(() => import("__JS_ROOT__/src/pages/dashboard/__LC_MODEL__/__MODEL__EditPage")));
const __MODEL__View = Loadable(lazy(() => import("__JS_ROOT__/src/pages/dashboard/__LC_MODEL__/__MODEL__ViewPage")));

export default [
    {
        path: "/__KEBAB_MODEL__/new",
        element: <__MODEL__Add/>
    },
    {
        path: "/__KEBAB_MODEL__/:id/edit",
        element: <__MODEL__Edit/>
    },
    {
        path: "/__KEBAB_MODEL__/:id",
        element: <__MODEL__View/>
    },
    {
        path: "/__KEBAB_MODEL__s",
        element: <__MODEL__List/>
    }
];
