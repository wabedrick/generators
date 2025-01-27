import React from "react";
import { View, Text, Image, Pressable, ScrollView } from "react-native";
import { SafeAreaView } from "react-native-safe-area-context";
import { FontAwesome } from "@expo/vector-icons";

const FoodDetailsScreen = ({ navigation, route }) => {
  const { item } = route.params;
  // console.log(item);
  return (
    <View style={{ backgroundColor: item.color, flex: 1 }}>
      <SafeAreaView
        style={{ flexDirection: "row", marginHorizontal: 16 }}
        key={item.id}
      >
        <Pressable style={{ flex: 1 }} onPress={() => navigation.goBack()}>
          <FontAwesome
            name="arrow-circle-left"
            size={30}
            style={{ color: "white" }}
          />
        </Pressable>

        <FontAwesome name="heart-o" size={30} style={{ color: "white" }} />
      </SafeAreaView>
      <View
        style={{
          backgroundColor: "#fff",
          flex: 1,
          marginTop: 240,
          borderTopLeftRadius: 56,
          borderTopRightRadius: 56,
          alignItems: "center",
          paddingHorizontal: 16,
        }}
      >
        <View
          style={{
            // backgroundColor: "red",
            position: "absolute",
            height: 300,
            width: 300,
            top: -150,
            // alignItems: "center",
          }}
        >
          <Image
            source={item.image}
            style={{
              height: "100%",
              width: "100%",
              resizeMode: "contain",
              borderRadius: 150,
              // transform: [{ rotate: "-15deg" }],
            }}
          />
        </View>
        {/* Food item name */}
        <Text
          style={{
            marginTop: 160,
            fontSize: 28,
            fontWeight: "bold",
            fontFamily: "serif",
          }}
        >
          {item.name}
        </Text>

        <View style={{ flex: 1 }}>
          <ScrollView showsVerticalScrollIndicator={false}>
            {/* Food Item description */}
            <Text
              style={{
                fontSize: 20,
                fontFamily: "serif",
                marginVertical: 12,
              }}
            >
              {item.description}
            </Text>

            <View
              style={{
                flexDirection: "row",
                width: "100%",
                justifyContent: "space-between",
              }}
            >
              <View
                style={{
                  backgroundColor: "rgba(255, 0, 0, 0.38)",
                  alignItems: "center",
                  // paddingHorizontal: 20,
                  paddingVertical: 26,
                  borderRadius: 8,
                  width: 120,
                }}
              >
                <Text style={{ fontSize: 40 }}>⏰</Text>
                <Text style={{ fontSize: 22, fontWeight: 500 }}>
                  {item.time}
                </Text>
              </View>

              <View
                style={{
                  backgroundColor: "rgba(135, 206, 235, 0.8)",
                  alignItems: "center",
                  // paddingHorizontal: 20,
                  paddingVertical: 26,
                  borderRadius: 8,
                  width: 120,
                }}
              >
                <Text style={{ fontSize: 40 }}>🍵</Text>
                <Text style={{ fontSize: 22, fontWeight: 500 }}>
                  {item.dificulty}
                </Text>
              </View>

              <View
                style={{
                  backgroundColor: "rgba(255, 165, 0, 0.48)",
                  alignItems: "center",
                  // paddingHorizontal: 20,
                  paddingVertical: 26,
                  borderRadius: 8,
                  width: 120,
                }}
              >
                <Text style={{ fontSize: 40 }}>🔥</Text>
                <Text style={{ fontSize: 22, fontWeight: 500 }}>
                  {item.calories}
                </Text>
              </View>
            </View>

            {/* Ingredients list */}
            <View style={{ alignSelf: "flex-start", marginVertical: 18 }}>
              <Text style={{ fontSize: 22, fontWeight: "600" }}>
                Ingredients
              </Text>
              {item.ingredients.map((ingredient) => {
                return (
                  <View style={{ flexDirection: "row", alignItems: "center" }}>
                    <View
                      style={{
                        backgroundColor: "red",
                        height: 10,
                        width: 10,
                        borderRadius: 5,
                      }}
                    ></View>
                    <Text style={{ fontSize: 16, marginLeft: 6 }}>
                      {ingredient}
                    </Text>
                  </View>
                );
              })}
            </View>

            {/* The steps to prepare a meal */}
            <View style={{ alignSelf: "flex-start", marginVertical: 18 }}>
              <Text style={{ fontSize: 22, fontWeight: "600" }}>
                Steps to follow
              </Text>
              {item.steps.map((step) => {
                return (
                  <View style={{ flexDirection: "row", alignItems: "center" }}>
                    <View
                      style={{
                        backgroundColor: "red",
                        height: 10,
                        width: 10,
                        borderRadius: 5,
                      }}
                    ></View>
                    <Text style={{ fontSize: 16, marginLeft: 6 }}>{step}</Text>
                  </View>
                );
              })}
            </View>
          </ScrollView>
        </View>
      </View>
    </View>
  );
};

export default FoodDetailsScreen;
