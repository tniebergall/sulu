// @flow
export type RouteConfig = {
    name: string,
    parent?: string,
    view: string,
    path: string,
    options: Object,
    attributeDefaults: AttributeMap,
};

export type Route = {
    name: string,
    parent: ?Route,
    children: Array<Route>,
    view: string,
    path: string,
    options: Object,
    attributeDefaults: AttributeMap,
};

export type AttributeMap = {[string]: string};

export type RouteMap = {[string]: Route};
