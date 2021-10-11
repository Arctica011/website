import React from "react";
import PropTypes from "prop-types";
import {CircularProgress, withStyles} from "@material-ui/core";
import api from "__JS_ROOT__/src/services/api";
import CreateUpdateForm from "__JS_ROOT__/src/lib/crud-tools/crud/CreateUpdateForm";
import __MODEL__Model from "__JS_ROOT__/src/generated/__LC_MODEL__/__MODEL__Model";

function __MODEL__Form(props) {
    const {
        classes,
        entity,
        onSaved,
        mode
    } = props;

    const values = mode === "create" ? {} : entity;
    const loading = mode === "update" && !entity;

    return (
        <div className={classes.container}>
            {loading && <CircularProgress/>}
            {!loading && <CreateUpdateForm
                crudApi={api.__LC_MODEL__}
                fields={__MODEL__Model.fields}
                mode={mode}
                defaultValues={values}
                onSaved={onSaved}
            />}
        </div>
    );
}

__MODEL__Form.propTypes = {
    __LC_MODEL__: PropTypes.object
};


const styles = {
    container: {
        minHeight: "50px",
        padding: "20px"
    }
};


export default withStyles(styles)(__MODEL__Form);
