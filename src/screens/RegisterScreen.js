import { React, useState } from "react";
import { View, Text, Image, TextInput, TouchableOpacity } from "react-native";
import { SafeAreaView } from "react-native-safe-area-context";
import Image1 from "../../assets/images/image1.jpeg";
import { registeredUsers, registerUser } from "../Constants";
import { useNavigation } from "@react-navigation/native";

const RegisterScreen = () => {
  const [uname, setUname] = useState("");
  const [phoneNumber, setPhoneNumber] = useState("");
  const [passwd, setPasswd] = useState("");
  const [showPassword, setShowPassword] = useState(true);

  const navigation = useNavigation();

  const registerdAlready = registeredUsers.find(
    (user) => user.username === uname || user.phoneNumber === phoneNumber
  );

  const handleRegister = () => {
    if (registerdAlready) {
      alert("Account already exists");
    } else if (uname !== "" || phoneNumber !== "" || passwd !== "") {
      registerUser(uname, passwd, phoneNumber);
      if (registerUser) {
        navigation.navigate("Login");
      } else {
        alert("Something went wrong");
      }
    } else {
      alert("Fill the form");
    }
  };
  return (
    <SafeAreaView style={{ flex: 1, marginTop: 60 }}>
      <View style={{ alignItems: "center", justifyContent: "center", flex: 1 }}>
        <Image
          source={Image1}
          style={{
            height: 250,
            width: 250,
            borderRadius: 150,
            marginBottom: 26,
          }}
        />
        <View>
          <Text style={{ fontSize: 26, fontWeight: "700" }}>Register Here</Text>

          <TextInput
            placeholder="Create Username"
            value={uname}
            onChangeText={setUname}
            style={{
              backgroundColor: "lightgray",
              color: "gray",
              paddingHorizontal: 16,
              paddingVertical: 16,
              borderRadius: 8,
              fontSize: 20,
              marginTop: 26,
              marginBottom: 16,
            }}
          />

          <TextInput
            placeholder="Phone Number or Email"
            value={phoneNumber}
            onChangeText={setPhoneNumber}
            style={{
              backgroundColor: "lightgray",
              color: "gray",
              paddingHorizontal: 16,
              paddingVertical: 16,
              borderRadius: 8,
              fontSize: 20,
              marginBottom: 16,
            }}
          />
          <View
            style={{
              backgroundColor: "lightgray",
              color: "gray",
              paddingHorizontal: 16,
              paddingVertical: 16,
              borderRadius: 8,
              flexDirection: "row",
              marginBottom: 16,
            }}
          >
            <TextInput
              placeholder="Create Password"
              secureTextEntry={showPassword}
              value={passwd}
              onChangeText={setPasswd}
              style={{ fontSize: 20, flex: 1 }}
            />
            <TouchableOpacity onPress={() => setShowPassword(!showPassword)}>
              <Text style={{ color: "green", fontSize: 20 }}>
                {passwd !== "" ? (showPassword ? "Show" : "Hide") : null}
              </Text>
            </TouchableOpacity>
          </View>

          <TouchableOpacity
            onPress={handleRegister}
            style={{
              backgroundColor: "#F96163",
              paddingHorizontal: 16,
              paddingVertical: 16,
              width: 320,
              borderRadius: 16,
              alignItems: "center",
            }}
          >
            <Text style={{ fontSize: 22, fontWeight: "600" }}>Register</Text>
          </TouchableOpacity>
        </View>
      </View>
    </SafeAreaView>
  );
};

export default RegisterScreen;
