import React from "react";
import PropTypes from "prop-types";
import {Box, CircularProgress, withStyles} from "@material-ui/core";
import FieldCollectionView from "__JS_ROOT__/src/lib/crud-tools/crud/FieldCollectionView";
import __MODEL__Model from "__JS_ROOT__/src/generated/__LC_MODEL__/__MODEL__Model";

function __MODEL__Details(props) {
    const {
        classes,
        entity
    } = props;


    return (
        <Box className={classes.container}>
            {!entity && <CircularProgress/>}
            {entity && (
                <FieldCollectionView
                    fields={__MODEL__Model.fields}
                    entity={entity}
                />
            )}
        </Box>
    );
}

__MODEL__Details.propTypes = {
    account: PropTypes.object
};


const styles = {
    container: {
        minHeight: "50px",
        justifyContent: "flex-start",
        alignItems: "center",
        display: "flex",
        padding: "0px 20px"
    }
};


export default withStyles(styles)(__MODEL__Details);
