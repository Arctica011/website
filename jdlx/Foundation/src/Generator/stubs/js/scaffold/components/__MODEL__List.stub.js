import React from "react";

import {withStyles} from "@material-ui/core";
import api from "__JS_ROOT__/src/services/api";
import __MODEL__Model from "__JS_ROOT__/src/generated/__LC_MODEL__/__MODEL__Model";
import CrudDataProvider from "__JS_ROOT__/src/lib/data-table/CrudDataProvider";
import ConfigProvider from "__JS_ROOT__/src/lib/data-table/ConfigProvider";
import ConfigFactory from "__JS_ROOT__/src/lib/data-table/ConfigFactory";
import asBareTable from "__JS_ROOT__/src/lib/data-table/builders/asBareTable";
import crudFields from "__JS_ROOT__/src/lib/data-table/builders/crudFields";
import crudActions from "__JS_ROOT__/src/lib/data-table/builders/crudActions";
import paging from "__JS_ROOT__/src/lib/data-table/builders/paging";
import sorting from "__JS_ROOT__/src/lib/data-table/builders/sorting";
import search from "__JS_ROOT__/src/lib/data-table/builders/filter";
import DataTable from "__JS_ROOT__/src/lib/data-table/DataTable";

class __MODEL__List extends React.Component {

    constructor(props) {
        super(props);
        this.state = this.createProviders();

    }

    createProviders() {
        const factory = new ConfigFactory();
        factory
            .with(asBareTable())
            .with(crudFields(__MODEL__Model.fields))
            .with(crudActions("__LC_MODEL__"))
            .with(paging())
            .with(sorting("name", "asc"))
            .with(search());

        const dataProvider = new CrudDataProvider(api.__LC_MODEL__, {});
        const configProvider = new ConfigProvider();

        factory.build(configProvider, dataProvider);
        return {configProvider, dataProvider};
    }

    render() {
        return (
            <DataTable
                configProvider={this.state.configProvider}
                dataProvider={this.state.dataProvider}
            />
        );
    }
}

__MODEL__List.propTypes = {

};

const styles = {};

export default withStyles(styles)(__MODEL__List);
