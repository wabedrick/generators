import { StyleSheet, Text, TouchableOpacity, View } from "react-native";
import { FontAwesome } from "@expo/vector-icons";
import React from "react";
import { useNavigation } from "@react-navigation/native";

const Header = ({ headerText, headerIcon }) => {
  const navigation = useNavigation();
  return (
    <View style={{ flexDirection: "row" }}>
      <Text style={{ fontSize: 22, marginRight: 4 }}>Hi,</Text>
      <Text
        style={{
          flex: 1,
          fontSize: 22,
          fontWeight: "700",
          letterSpacing: 0.5,
          fontFamily: "serif",
        }}
      >
        {headerText}
      </Text>
      <FontAwesome name={headerIcon} size={24} color="#f96163" />
      <TouchableOpacity onPress={() => navigation.navigate("welcome")}>
        <Text
          style={{
            marginLeft: 8,
            fontSize: 18,
            color: "lightgreen",
            fontWeight: "bold",
          }}
        >
          Logout
        </Text>
      </TouchableOpacity>
    </View>
  );
};

export default Header;

const styles = StyleSheet.create({});
