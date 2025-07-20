import { capitalize } from "lodash";
import type { RouteRecordRaw, RouteMeta } from "vue-router";

interface CreateCRUDRoutesOptions {
    root?: boolean;
    meta?: {
        index?: RouteMeta;
        create?: RouteMeta;
        detail?: RouteMeta;
    };
    props?: {
        index?: Record<string, unknown>;
        create?: Record<string, unknown>;
        detail?: Record<string, unknown>;
    };
    prefix: "account" | "admin" | "site";
}

export const createCRUDRoutes = (
    path: string,
    {
        root = false,
        meta = {
            index: undefined,
            create: undefined,
            detail: undefined,
        },
        prefix,
    }: CreateCRUDRoutesOptions,
): RouteRecordRaw[] => {
    const componentKey = `${capitalize(path)}`;

    const indexRoute: RouteRecordRaw = {
        path: `/${path}`,
        name: `${componentKey}`,
        meta: meta.index,
        component: () =>
            import(`@/${prefix}/ts/pages/${componentKey}/List.vue`),
    };
    if (root) {
        indexRoute.alias = `/`;
    }

    const routes: RouteRecordRaw[] = [
        indexRoute,
        {
            path: `/${path}/create`,
            name: `${componentKey}Create`,
            meta: meta.create,
            component: () =>
                import(`@/${prefix}/ts/pages/${componentKey}/Detail.vue`),
        },
        {
            path: `/${path}/:id`,
            name: `${componentKey}Detail`,
            meta: meta.detail,
            component: () =>
                import(`@/${prefix}/ts/pages/${componentKey}/Detail.vue`),
        },
    ];

    return routes;
};
