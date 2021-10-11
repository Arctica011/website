import React from "react";
import PropTypes from "prop-types";
import {Box, Button, Divider, Icon, Paper, Toolbar, Typography, withStyles} from "@material-ui/core";
import {useNavigate} from "react-router";
import __MODEL__Details from "./__MODEL__Details";

function __MODEL__ViewCard(props) {
    const {
        classes,
        entity
    } = props;


    const navigate = useNavigate();

    return (
        <Paper>
            <Toolbar className={classes.toolbar}>
                <Typography variant="h6">{(entity?.name) ?? "__MODEL__"}</Typography>
            </Toolbar>
            <Divider/>
            <Box className={classes.container}>
                <__MODEL__Details entity={entity}/>
            </Box>
            <Box className={classes.actions}>
                <Button
                    variant={'contained'}
                    color="primary"
                    onClick={() => navigate(`edit`)}
                >
                    <Icon>edit</Icon> Edit
                </Button>
            </Box>
        </Paper>
    );
}

__MODEL__ViewCard.propTypes = {
    account: PropTypes.object
};


const styles = {
    toolbar: {
        justifyContent: "space-between",
    },
    container: {
        minHeight: "50px",
        justifyContent: "flex-start",
        alignItems: "center",
        display: "flex",
        padding: "0px 20px"
    },
    divider: {
        backgroundColor: "rgba(0, 0, 0, 0.26)"
    },
    actions: {
        textAlign: "center",
        padding: "20px 0px"
    }
};


export default withStyles(styles)(__MODEL__ViewCard);
