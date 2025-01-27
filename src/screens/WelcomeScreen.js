import React from "react";
import { StyleSheet, View, Image, Text, TouchableOpacity } from "react-native";
import { SafeAreaView } from "react-native-safe-area-context";
import Image1 from "../../assets/images/image1.jpeg";

const WelcomeScreen = ({ navigation }) => {
  return (
    <SafeAreaView style={{ flex: 1, alignItems: "center" }}>
      <Image
        source={Image1}
        style={{ height: 320, width: 380, borderRadius: 50 }}
      />
      <Text
        style={{
          fontSize: 22,
          color: "#F96163",
          fontWeight: "bold",
          marginTop: 60,
          marginBottom: 60,
        }}
      >
        40K+ Premium Recipes
      </Text>
      <Text
        style={{
          color: "#3c444c",
          fontSize: 44,
          fontWeight: "bold",
          marginBottom: 70,
        }}
      >
        Cook Like A Chef
      </Text>

      <TouchableOpacity
        onPress={() => navigation.navigate("Login")}
        style={{
          backgroundColor: "#F96163",
          paddingVertical: 18,
          width: "80%",
          alignItems: "center",
          borderRadius: 18,
          marginBottom: 8,
        }}
      >
        <Text style={{ color: "#fff", fontSize: 22, fontWeight: "bold" }}>
          Login
        </Text>
      </TouchableOpacity>

      <TouchableOpacity
        onPress={() => navigation.navigate("Register")}
        style={{
          backgroundColor: "#F96163",
          paddingVertical: 18,
          width: "80%",
          alignItems: "center",
          borderRadius: 18,
        }}
      >
        <Text style={{ color: "#fff", fontSize: 22, fontWeight: "bold" }}>
          Register
        </Text>
      </TouchableOpacity>
    </SafeAreaView>
  );
};

export default WelcomeScreen;

const styles = StyleSheet.create({});
