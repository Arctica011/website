import React, {useCallback} from "react";
import {Divider, Paper, Toolbar, Typography, withStyles} from "@material-ui/core";
import {useNavigate} from "react-router";
import __MODEL__Form from "./__MODEL__Form";

function __MODEL__AddCard(props) {
    const {
        classes
    } = props;

    const navigate = useNavigate();

    const onSaved = useCallback(() => {
        navigate("../../__KEBAB_MODEL__s");
    }, [navigate]);

    return (
        <Paper>
            <Toolbar className={classes.toolbar}>
                <Typography variant="h6">Add __HUMAN_MODEL__</Typography>
            </Toolbar>
            <Divider/>
            <div className={classes.container}>
                <__MODEL__Form
                    onSaved={onSaved}
                    mode={"create"}
                />
            </div>
        </Paper>
    );
}

__MODEL__AddCard.propTypes = {};


const styles = {
    toolbar: {
        justifyContent: "space-between",
    },
    container: {
        minHeight: "50px",
        padding: "20px"
    },
    divider: {
        backgroundColor: "rgba(0, 0, 0, 0.26)"
    },
    actions: {
        textAlign: "center",
        padding: "20px 0px"
    }
};


export default withStyles(styles)(__MODEL__AddCard);
